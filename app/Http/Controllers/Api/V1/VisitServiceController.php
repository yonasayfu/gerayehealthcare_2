<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\StoreVisitServiceRequest;
use App\Http\Requests\Api\V1\UpdateVisitServiceRequest;
use App\Http\Requests\VisitService\CheckInRequest;
use App\Http\Requests\VisitService\CheckOutRequest;
use App\Http\Resources\VisitServiceResource;
use App\Models\Patient;
use App\Models\VisitService;
use App\Services\VisitService\VisitServiceService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;

class VisitServiceController extends BaseApiController
{
    public function __construct(private readonly VisitServiceService $visitServiceService)
    {
    }

    public function index(Request $request)
    {
        $visits = $this->visitServiceService->getAll($request);
        $resource = VisitServiceResource::collection($visits)->response()->getData(true);

        return $this->successResponse([
            'visits' => $resource['data'] ?? [],
            'pagination' => [
                'current_page' => $visits->currentPage(),
                'last_page' => $visits->lastPage(),
                'per_page' => $visits->perPage(),
                'total' => $visits->total(),
                'from' => $visits->firstItem(),
                'to' => $visits->lastItem(),
                'has_more' => $visits->hasMorePages(),
            ],
        ]);
    }

    public function show(VisitService $visitService)
    {
        $visitService->load(['patient', 'staff']);
        $payload = (new VisitServiceResource($visitService))->response()->getData(true);

        return $this->successResponse([
            'visit' => $payload['data'] ?? null,
        ]);
    }

    public function store(StoreVisitServiceRequest $request)
    {
        $validated = $request->validated();
        $patientId = $validated['patient_id'] ?? null;

        if (! $patientId) {
            $patientId = optional(Patient::where('email', $request->user()->email)->first())->id;
        }

        if (! Gate::forUser($request->user())->allows('create', [VisitService::class, $patientId])) {
            return $this->forbiddenResponse();
        }

        try {
            $payload = array_merge($validated, [
                'patient_id' => $patientId,
                'staff_id' => $validated['staff_id'] ?? optional($request->user()->staff)->id,
                'status' => $validated['status'] ?? 'Scheduled',
                'priority' => $validated['priority'] ?? 'normal',
                'service_type' => $validated['service_type'] ?? 'general',
            ]);

            if (! array_key_exists('follow_up_required', $payload)) {
                $payload['follow_up_required'] = false;
            }

            $visit = $this->visitServiceService->create($payload)->load(['patient', 'staff']);
            $resource = (new VisitServiceResource($visit))->response()->getData(true);

            return $this->createdResponse([
                'visit' => $resource['data'] ?? null,
            ], 'Visit scheduled successfully');
        } catch (\Throwable $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function update(UpdateVisitServiceRequest $request, VisitService $visitService)
    {
        $this->authorize('update', $visitService);

        try {
            $validated = $request->validated();
            if (isset($validated['scheduled_at']) && ! isset($validated['staff_id'])) {
                $validated['staff_id'] = $visitService->staff_id;
            }

            if (! array_key_exists('follow_up_required', $validated)) {
                $validated['follow_up_required'] = $visitService->follow_up_required;
            }

            $validated['service_type'] = $validated['service_type'] ?? $visitService->service_type ?? 'general';
            $validated['priority'] = $validated['priority'] ?? $visitService->priority ?? 'normal';

            $updated = $this->visitServiceService->update($visitService->id, $validated)->fresh(['patient', 'staff']);
            $resource = (new VisitServiceResource($updated))->response()->getData(true);

            return $this->successResponse([
                'message' => 'Visit updated successfully',
                'visit' => $resource['data'] ?? null,
            ]);
        } catch (\Throwable $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function destroy(Request $request, VisitService $visitService)
    {
        $this->authorize('cancel', $visitService);

        $visitService->update([
            'status' => 'Cancelled',
            'cancellation_reason' => $request->input('cancellation_reason'),
            'cancelled_at' => now(),
            'cancelled_by' => optional($request->user())->id,
        ]);

        return $this->successResponse([
            'message' => 'Visit cancelled successfully',
        ]);
    }

    public function mySchedule(Request $request)
    {
        $user = $request->user();

        $query = VisitService::with(['patient', 'staff'])
            ->when($user->staff, function ($q) use ($user) {
                $q->where('staff_id', $user->staff->id);
            }, function ($q) use ($user) {
                $q->whereHas('patient', function ($pq) use ($user) {
                    $pq->where('user_id', $user->id);
                });
            });

        if ($request->filled('date')) {
            $query->whereDate('scheduled_at', $request->input('date'));
        }

        $visits = $query->orderBy('scheduled_at', 'asc')->paginate($request->integer('per_page', 10));
        $resource = VisitServiceResource::collection($visits)->response()->getData(true);

        return $this->successResponse([
            'visits' => $resource['data'] ?? [],
            'pagination' => [
                'current_page' => $visits->currentPage(),
                'last_page' => $visits->lastPage(),
                'per_page' => $visits->perPage(),
                'total' => $visits->total(),
                'from' => $visits->firstItem(),
                'to' => $visits->lastItem(),
                'has_more' => $visits->hasMorePages(),
            ],
        ]);
    }

    public function checkIn(CheckInRequest $request, VisitService $visitService)
    {
        $now = now();
        $clientTs = $request->input('timestamp');
        $checkInTime = $now;

        if ($clientTs) {
            try {
                $parsed = Carbon::parse($clientTs);
                if ($parsed->lte($now->copy()->addMinutes(5)) && $parsed->gte($now->copy()->subHours(12))) {
                    $checkInTime = $parsed;
                }
            } catch (\Throwable $e) {
                // Ignore parsing errors, use server time
            }
        }

        $visitService->update([
            'check_in_time' => $checkInTime,
            'check_in_latitude' => $request->input('latitude'),
            'check_in_longitude' => $request->input('longitude'),
            'status' => $visitService->status === 'Scheduled' ? 'In Progress' : $visitService->status,
        ]);

        $resource = (new VisitServiceResource($visitService->fresh(['patient', 'staff'])))->response()->getData(true);

        return $this->successResponse([
            'message' => 'Visit checked in successfully',
            'visit' => $resource['data'] ?? null,
        ]);
    }

    public function checkOut(CheckOutRequest $request, VisitService $visitService)
    {
        $now = now();
        $clientTs = $request->input('timestamp');
        $outTime = $now;

        if ($clientTs) {
            try {
                $parsed = Carbon::parse($clientTs);
                if ($parsed->lte($now->copy()->addMinutes(5)) && $parsed->gte($now->copy()->subHours(24))) {
                    $outTime = $parsed;
                }
            } catch (\Throwable $e) {
                // Ignore parsing errors, use server time
            }
        }

        $checkIn = $visitService->check_in_time ? Carbon::parse($visitService->check_in_time) : $outTime;
        if ($outTime->lt($checkIn)) {
            $outTime = $checkIn;
        }

        $durationHours = max(0, $checkIn->floatDiffInRealHours($outTime));
        $hourlyRate = optional($visitService->staff)->hourly_rate ?? 0;
        $earned = round($durationHours * (float) $hourlyRate, 2);

        $visitService->update([
            'check_out_time' => $outTime,
            'check_out_latitude' => $request->input('latitude'),
            'check_out_longitude' => $request->input('longitude'),
            'status' => 'Completed',
            'cost' => $earned,
        ]);

        $resource = (new VisitServiceResource($visitService->fresh(['patient', 'staff'])))->response()->getData(true);

        return $this->successResponse([
            'message' => 'Visit checked out successfully',
            'visit' => $resource['data'] ?? null,
        ]);
    }
}
