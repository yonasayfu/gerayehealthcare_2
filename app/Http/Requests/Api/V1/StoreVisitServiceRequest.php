<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreVisitServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['nullable', 'integer', 'exists:patients,id'],
            'staff_id' => ['nullable', 'integer', 'exists:staff,id'],
            'scheduled_at' => ['required', 'date'],
            'service_type' => ['nullable', 'string', 'max:100'],
            'priority' => ['nullable', 'string', 'max:50'],
            'visit_location' => ['nullable', 'string', 'max:255'],
            'patient_location' => ['nullable', 'string', 'max:255'],
            'gps_coordinates' => ['nullable', 'string', 'max:255'],
            'visit_notes' => ['nullable', 'string'],
            'patient_condition' => ['nullable', 'string'],
            'treatment_provided' => ['nullable', 'string'],
            'medications_administered' => ['nullable', 'string'],
            'follow_up_required' => ['nullable', 'boolean'],
            'follow_up_date' => ['nullable', 'date'],
            'follow_up_notes' => ['nullable', 'string'],
            'payment_status' => ['nullable', 'string', 'max:50'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'insurance_claim_id' => ['nullable', 'integer', 'exists:insurance_claims,id'],
            'rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'feedback' => ['nullable', 'string'],
        ];
    }
}
