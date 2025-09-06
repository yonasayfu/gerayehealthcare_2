<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CaregiverAssignmentResource;
use App\Http\Resources\PatientBriefResource;
use App\Models\CaregiverAssignment;
use Illuminate\Http\Request;

class CaregiverAssignmentController extends Controller
{
    public function myActive(Request $request)
    {
        $user = $request->user();
        $assignments = CaregiverAssignment::with(['patient', 'staff'])
            ->when($user->staff, function ($q) use ($user) {
                $q->where('staff_id', $user->staff->id);
            })
            ->where('status', 'Assigned')
            ->orderByDesc('id')
            ->paginate($request->integer('per_page', 10));

        return CaregiverAssignmentResource::collection($assignments);
    }

    public function myPatients(Request $request)
    {
        $user = $request->user();
        $patients = CaregiverAssignment::with('patient')
            ->when($user->staff, function ($q) use ($user) {
                $q->where('staff_id', $user->staff->id);
            })
            ->where('status', 'Assigned')
            ->get()
            ->pluck('patient')
            ->unique('id')
            ->values();

        return PatientBriefResource::collection($patients);
    }
}
