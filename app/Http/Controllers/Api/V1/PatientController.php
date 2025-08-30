<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PatientResource;
use App\Models\Patient;
use App\Services\PatientService;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    protected $patientService;

    public function __construct(PatientService $patientService)
    {
        $this->patientService = $patientService;
    }

    public function index(Request $request)
    {
        $patients = $this->patientService->getAll($request);
        return PatientResource::collection($patients);
    }

    public function show(Patient $patient)
    {
        return new PatientResource($patient);
    }

    // Return the patient record linked to the authenticated user (if exists)
    public function me(Request $request)
    {
        $user = $request->user();
        $patient = Patient::where('email', $user->email)->first();

        if (!$patient) {
            return response()->json(['message' => 'Patient record not found for this user'], 404);
        }

        return new PatientResource($patient);
    }

    // Update limited fields on the authenticated user's patient profile
    public function updateMe(Request $request)
    {
        $validated = $request->validate([
            'full_name' => ['sometimes', 'string', 'max:255'],
            'phone_number' => ['sometimes', 'string', 'max:255'],
            'address' => ['sometimes', 'string', 'max:1000'],
        ]);

        $user = $request->user();
        $patient = Patient::where('email', $user->email)->first();

        if (!$patient) {
            return response()->json(['message' => 'Patient record not found for this user'], 404);
        }

        $this->patientService->update($patient->id, $validated);
        $patient->refresh();

        return new PatientResource($patient);
    }
}
