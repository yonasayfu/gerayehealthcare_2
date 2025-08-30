<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\VisitService\CheckInRequest;
use App\Http\Requests\VisitService\CheckOutRequest;
use App\Http\Resources\VisitServiceResource;
use App\Models\VisitService;
use App\Models\Patient;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

class VisitServiceController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => ['nullable', 'integer', 'exists:patients,id'],
            'scheduled_at' => ['required', 'date'],
            'service_description' => ['nullable', 'string', 'max:1000'],
        ]);

        // Determine patient_id: if not provided, infer from user email
        $patientId = $validated['patient_id'] ?? null;
        if (!$patientId) {
            $patientId = optional(Patient::where('email', $request->user()->email)->first())->id;
        }

        if (!Gate::forUser($request->user())->allows('create', [VisitService::class, $patientId])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $visit = VisitService::create([
            'patient_id' => $patientId,
            'staff_id' => optional($request->user()->staff)->id, // if staff books for themselves
            'scheduled_at' => $validated['scheduled_at'],
            'service_description' => $validated['service_description'] ?? null,
            'status' => 'Scheduled',
        ]);

        return new VisitServiceResource($visit->load(['patient', 'staff']));
    }

    public function update(Request $request, VisitService $visitService)
    {
        $this->authorize('update', $visitService);

        $validated = $request->validate([
            'scheduled_at' => ['sometimes', 'date'],
            'service_description' => ['sometimes', 'string', 'max:1000'],
        ]);

        $visitService->update($validated);
        return new VisitServiceResource($visitService->fresh(['patient', 'staff']));
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
                // If not staff, try to scope by patient email
                $q->whereHas('patient', function ($pq) use ($user) {
                    $pq->where('email', $user->email);
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
        $visitService->update([
            'check_out_time' => now(),
            'check_out_latitude' => $request->input('latitude'),
            'check_out_longitude' => $request->input('longitude'),
            'status' => 'Completed',
        ]);

        return new VisitServiceResource($visitService->fresh(['patient', 'staff']));
    }
}
