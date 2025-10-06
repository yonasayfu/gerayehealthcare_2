<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\UpdateSelfPatientRequest;
use App\Http\Resources\PatientResource;
use App\Models\Patient;
use App\Services\Patient\PatientService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class PatientController extends BaseApiController
{
    public function __construct(private readonly PatientService $patientService)
    {
    }

    public function index(Request $request)
    {
        $patients = $this->patientService->getAll($request);
        $resource = PatientResource::collection($patients)->response()->getData(true);

        return $this->successResponse([
            'patients' => $resource['data'] ?? [],
            'pagination' => [
                'current_page' => $patients->currentPage(),
                'last_page' => $patients->lastPage(),
                'per_page' => $patients->perPage(),
                'total' => $patients->total(),
                'from' => $patients->firstItem(),
                'to' => $patients->lastItem(),
                'has_more' => $patients->hasMorePages(),
            ],
        ]);
    }

    public function show(Patient $patient)
    {
        $payload = (new PatientResource($patient))->response()->getData(true);

        return $this->successResponse([
            'patient' => $payload['data'] ?? null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->normalizePayload(
            $this->validate($request, $this->creationRules())
        );

        if (! isset($validated['status'])) {
            $validated['status'] = 'active';
        }

        $patient = $this->patientService->create($validated)->fresh();
        $payload = (new PatientResource($patient))->response()->getData(true);

        return $this->createdResponse([
            'patient' => $payload['data'] ?? null,
        ], 'Patient created successfully');
    }

    public function update(Request $request, Patient $patient)
    {
        $validated = $this->normalizePayload(
            $this->validate($request, $this->updateRules($patient))
        );

        $updated = $this->patientService->update($patient->id, $validated);
        $payload = (new PatientResource($updated))->response()->getData(true);

        return $this->successResponse([
            'message' => 'Patient updated successfully',
            'patient' => $payload['data'] ?? null,
        ]);
    }

    public function destroy(Patient $patient)
    {
        $this->patientService->delete($patient->id);

        return $this->successResponse([
            'message' => 'Patient deleted successfully',
        ], Response::HTTP_OK);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        $patient = Patient::where('user_id', $user->id)->first();

        if (! $patient) {
            return $this->notFoundResponse('Patient record not found for this user');
        }

        $payload = (new PatientResource($patient))->response()->getData(true);

        return $this->successResponse([
            'patient' => $payload['data'] ?? null,
        ]);
    }

    public function updateMe(UpdateSelfPatientRequest $request)
    {
        $user = $request->user();
        $patient = Patient::where('user_id', $user->id)->first();

        if (! $patient) {
            return $this->notFoundResponse('Patient record not found for this user');
        }

        $validated = $request->validated();
        if ($request->has('phone_number')) {
            $this->validate($request, [
                'phone_number' => [
                    'sometimes',
                    'string',
                    'max:50',
                    Rule::unique('patients', 'phone_number')->ignore($patient->id),
                ],
            ]);
            $validated['phone_number'] = $request->input('phone_number');
        }
        if ($request->has('email')) {
            $this->validate($request, [
                'email' => [
                    'sometimes',
                    'email',
                    'max:255',
                    Rule::unique('patients', 'email')->ignore($patient->id),
                ],
            ]);
            $validated['email'] = $request->input('email');
        }

        $this->patientService->update($patient->id, $validated);
        $payload = (new PatientResource($patient->fresh()))->response()->getData(true);

        return $this->successResponse([
            'message' => 'Profile updated successfully',
            'patient' => $payload['data'] ?? null,
        ]);
    }

    private function creationRules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:patients,email'],
            'phone_number' => ['nullable', 'string', 'max:50', 'unique:patients,phone_number'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:120'],
            'state' => ['nullable', 'string', 'max:120'],
            'country' => ['nullable', 'string', 'max:120'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'fayda_id' => ['nullable', 'string', 'max:100'],
            'patient_code' => ['nullable', 'string', 'max:100', 'unique:patients,patient_code'],
            'emergency_contact' => ['nullable', 'string'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:50'],
            'emergency_contact_relationship' => ['nullable', 'string', 'max:120'],
            'medical_history' => ['nullable', 'string'],
            'allergies' => ['nullable', 'string'],
            'current_medications' => ['nullable', 'string'],
            'insurance_provider' => ['nullable', 'string', 'max:255'],
            'insurance_policy_number' => ['nullable', 'string', 'max:100'],
            'preferred_language' => ['nullable', 'string', 'max:100'],
            'blood_type' => ['nullable', 'string', 'max:5'],
            'height' => ['nullable', 'numeric', 'min:0', 'max:300'],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:500'],
            'status' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
            'source' => ['nullable', 'string', 'max:255'],
            'geolocation' => ['nullable', 'string'],
            'registered_by_staff_id' => ['nullable', 'integer', 'exists:staff,id'],
            'acquisition_source_id' => ['nullable', 'integer', 'exists:lead_sources,id'],
            'marketing_campaign_id' => ['nullable', 'integer', 'exists:marketing_campaigns,id'],
            'utm_campaign' => ['nullable', 'string', 'max:255'],
            'utm_source' => ['nullable', 'string', 'max:255'],
            'utm_medium' => ['nullable', 'string', 'max:255'],
            'lead_id' => ['nullable', 'integer', 'exists:marketing_leads,id'],
            'acquisition_cost' => ['nullable', 'numeric'],
            'acquisition_date' => ['nullable', 'date'],
        ];
    }

    private function updateRules(Patient $patient): array
    {
        return [
            'full_name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('patients', 'email')->ignore($patient->id),
            ],
            'phone_number' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('patients', 'phone_number')->ignore($patient->id),
            ],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:120'],
            'state' => ['nullable', 'string', 'max:120'],
            'country' => ['nullable', 'string', 'max:120'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'fayda_id' => ['nullable', 'string', 'max:100'],
            'patient_code' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('patients', 'patient_code')->ignore($patient->id),
            ],
            'emergency_contact' => ['nullable', 'string'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:50'],
            'emergency_contact_relationship' => ['nullable', 'string', 'max:120'],
            'medical_history' => ['nullable', 'string'],
            'allergies' => ['nullable', 'string'],
            'current_medications' => ['nullable', 'string'],
            'insurance_provider' => ['nullable', 'string', 'max:255'],
            'insurance_policy_number' => ['nullable', 'string', 'max:100'],
            'preferred_language' => ['nullable', 'string', 'max:100'],
            'blood_type' => ['nullable', 'string', 'max:5'],
            'height' => ['nullable', 'numeric', 'min:0', 'max:300'],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:500'],
            'status' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
            'source' => ['nullable', 'string', 'max:255'],
            'geolocation' => ['nullable', 'string'],
            'registered_by_staff_id' => ['nullable', 'integer', 'exists:staff,id'],
            'acquisition_source_id' => ['nullable', 'integer', 'exists:lead_sources,id'],
            'marketing_campaign_id' => ['nullable', 'integer', 'exists:marketing_campaigns,id'],
            'utm_campaign' => ['nullable', 'string', 'max:255'],
            'utm_source' => ['nullable', 'string', 'max:255'],
            'utm_medium' => ['nullable', 'string', 'max:255'],
            'lead_id' => ['nullable', 'integer', 'exists:marketing_leads,id'],
            'acquisition_cost' => ['nullable', 'numeric'],
            'acquisition_date' => ['nullable', 'date'],
        ];
    }

    private function normalizePayload(array $data): array
    {
        $contact = array_filter([
            'name' => $data['emergency_contact_name'] ?? null,
            'phone' => $data['emergency_contact_phone'] ?? null,
            'relationship' => $data['emergency_contact_relationship'] ?? null,
        ], fn ($value) => filled($value));

        if (! empty($contact)) {
            $data['emergency_contact'] = json_encode($contact);
        }

        return $data;
    }
}
