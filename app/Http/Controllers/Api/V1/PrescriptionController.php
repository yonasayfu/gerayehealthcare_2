<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $q = Prescription::with(['patient:id,full_name,patient_code','createdBy:id,first_name,last_name'])
            ->orderByDesc('prescribed_date');

        if ($user->staff) {
            $q->where('created_by_staff_id', $user->staff->id);
        } elseif ($user->patient) {
            $q->where('patient_id', $user->patient->id);
        } else {
            return response()->json(['data' => []]);
        }

        return response()->json([
            'data' => $q->paginate((int) $request->input('per_page', 10)),
        ]);
    }

    public function show(Request $request, Prescription $prescription)
    {
        $user = $request->user();
        if ($user->staff && $prescription->created_by_staff_id !== $user->staff->id) {
            abort(403);
        }
        if ($user->patient && $prescription->patient_id !== $user->patient->id) {
            abort(403);
        }
        return response()->json($prescription->load(['items','patient:id,full_name,patient_code','createdBy:id,first_name,last_name']));
    }
}
