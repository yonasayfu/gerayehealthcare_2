<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreVisitServiceRequest;
use App\Http\Requests\Api\V1\UpdateVisitServiceRequest;
use App\Http\Requests\VisitService\CheckInRequest;
use App\Http\Requests\VisitService\CheckOutRequest;
use App\Http\Resources\VisitServiceResource;
use App\Models\Patient;
use App\Models\VisitService;
use App\Services\VisitServiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class VisitServiceController extends Controller
{
    public function __construct(private VisitServiceService $visitServiceService)
    {
    }
    public function store(StoreVisitServiceRequest $request)
    {
        // Determine patient_id: if not provided, infer from user email
        $patientId = $request->validated()['patient_id'] ?? null;
        if (! $patientId) {
            $patientId = optional(Patient::where('email', $request->user()->email)->first())->id;
        }

        if (! Gate::forUser($request->user())->allows('create', [VisitService::class, $patientId])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        try {
            $payload = [
                'patient_id' => $patientId,
                'staff_id' => optional($request->user()->staff)->id, // if staff books for themselves
                'scheduled_at' => $request->validated()['scheduled_at'],
                'service_description' => $request->validated()['service_description'] ?? null,
                'status' => 'Scheduled',
            ];
            $visit = $this->visitServiceService->create($payload);

            return new VisitServiceResource($visit->load(['patient', 'staff']));
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function update(UpdateVisitServiceRequest $request, VisitService $visitService)
    {
        $this->authorize('update', $visitService);

        try {
            $validated = $request->validated();
            // Ensure staff_id is present when scheduled_at changes so availability validation runs
            if (isset($validated['scheduled_at']) && ! isset($validated['staff_id'])) {
                $validated['staff_id'] = $visitService->staff_id;
            }
            $updated = $this->visitServiceService->update($visitService->id, $validated);

            return new VisitServiceResource($updated->fresh(['patient', 'staff']));
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(Request $request, VisitService $visitService)
    {
        $this->authorize('cancel', $visitService);
        $visitService->update(['status' => 'Cancelled']);

        return response()->noContent();
    }

    public function mySchedule(Request $request)
    {
        $user = $request->user();

        $query = VisitService::with(['patient', 'staff'])
            ->when($user->staff, function ($q) use ($user) {
                $q->where('staff_id', $user->staff->id);
            }, function ($q) use ($user) {
                // If not staff, scope by patient user_id
                $q->whereHas('patient', function ($pq) use ($user) {
                    $pq->where('user_id', $user->id);
                });
            });

        // Optional date filter: ?date=YYYY-MM-DD
        if ($request->filled('date')) {
            $query->whereDate('scheduled_at', $request->input('date'));
        }

        $visits = $query->orderBy('scheduled_at', 'asc')->paginate($request->integer('per_page', 10));

        return VisitServiceResource::collection($visits);
    }

    public function checkIn(CheckInRequest $request, VisitService $visitService)
    {
        $visitService->update([
            'check_in_time' => now(),
            'check_in_latitude' => $request->input('latitude'),
            'check_in_longitude' => $request->input('longitude'),
            'status' => $visitService->status === 'Scheduled' ? 'In Progress' : $visitService->status,
        ]);

        return new VisitServiceResource($visitService->fresh(['patient', 'staff']));
    }

    public function checkOut(CheckOutRequest $request, VisitService $visitService)
    {
        $now = now();
        $checkIn = $visitService->check_in_time ? \Illuminate\Support\Carbon::parse($visitService->check_in_time) : $now;
        $durationHours = max(0, $checkIn->floatDiffInRealHours($now));
        $hourlyRate = optional($visitService->staff)->hourly_rate ?? 0;
        $earned = round($durationHours * (float) $hourlyRate, 2);

        $visitService->update([
            'check_out_time' => $now,
            'check_out_latitude' => $request->input('latitude'),
            'check_out_longitude' => $request->input('longitude'),
            'status' => 'Completed',
            'cost' => $earned,
        ]);

        return new VisitServiceResource($visitService->fresh(['patient', 'staff']));
    }
}
