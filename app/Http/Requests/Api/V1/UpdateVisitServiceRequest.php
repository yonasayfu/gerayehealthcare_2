<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVisitServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['sometimes', 'integer', 'exists:patients,id'],
            'staff_id' => ['sometimes', 'integer', 'exists:staff,id'],
            'scheduled_at' => ['sometimes', 'date'],
            'service_type' => ['sometimes', 'string', 'max:100'],
            'priority' => ['sometimes', 'string', 'max:50'],
            'visit_location' => ['sometimes', 'string', 'max:255'],
            'patient_location' => ['sometimes', 'string', 'max:255'],
            'gps_coordinates' => ['sometimes', 'string', 'max:255'],
            'visit_notes' => ['sometimes', 'string'],
            'patient_condition' => ['sometimes', 'string'],
            'treatment_provided' => ['sometimes', 'string'],
            'medications_administered' => ['sometimes', 'string'],
            'follow_up_required' => ['sometimes', 'boolean'],
            'follow_up_date' => ['sometimes', 'date', 'nullable'],
            'follow_up_notes' => ['sometimes', 'string'],
            'payment_status' => ['sometimes', 'string', 'max:50'],
            'payment_method' => ['sometimes', 'string', 'max:50'],
            'insurance_claim_id' => ['sometimes', 'integer', 'nullable', 'exists:insurance_claims,id'],
            'rating' => ['sometimes', 'numeric', 'min:0', 'max:5'],
            'feedback' => ['sometimes', 'string'],
            'check_in_time' => ['sometimes', 'date'],
            'check_out_time' => ['sometimes', 'date'],
            'check_in_latitude' => ['sometimes', 'numeric', 'between:-90,90'],
            'check_in_longitude' => ['sometimes', 'numeric', 'between:-180,180'],
            'check_out_latitude' => ['sometimes', 'numeric', 'between:-90,90'],
            'check_out_longitude' => ['sometimes', 'numeric', 'between:-180,180'],
            'check_in_location' => ['sometimes', 'string', 'max:255'],
            'check_out_location' => ['sometimes', 'string', 'max:255'],
            'cancellation_reason' => ['sometimes', 'string'],
        ];
    }
}
