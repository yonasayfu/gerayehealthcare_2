<?php

namespace App\Services\Validation\Rules;

use Illuminate\Validation\Rule;

class PatientRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'full_name' => 'required|string|max:255',
            'fayda_id' => 'nullable|string|max:255|unique:patients,fayda_id',
            'date_of_birth' => 'required|date',
            'ethiopian_date_of_birth' => 'nullable|string',
            'gender' => 'nullable|string|max:10',
            'address' => 'nullable|string',
            'phone_number' => 'nullable|string|regex:/^[0-9+\-\s\(\)]+$/|max:255',
            // Relax DNS validation to avoid false negatives in non-public/test domains
            'email' => 'nullable|email:rfc|max:255|unique:patients,email',
            'emergency_contact' => 'nullable|string',
            'source' => 'nullable|string|max:255|in:TikTok,Website,Referral,Walk-in',
            'geolocation' => 'nullable|string',
            'registered_by_staff_id' => 'nullable|integer|exists:staff,id',
            'corporate_client_id' => 'nullable|integer|exists:corporate_clients,id',
            'policy_id' => 'nullable|integer|exists:insurance_policies,id',
        ];
    }

    public static function update($patient): array
    {
        return [
            'full_name' => 'required|string|max:255',
            'fayda_id' => ['nullable', 'string', 'max:255', Rule::unique('patients')->ignore($patient->id)],
            'date_of_birth' => 'required|date',
            'ethiopian_date_of_birth' => 'nullable|string',
            'gender' => 'nullable|string|max:10',
            'address' => 'nullable|string',
            'phone_number' => 'nullable|string|regex:/^[0-9+\-\s\(\)]+$/|max:255',
            // Relax DNS validation to avoid false negatives in non-public/test domains
            'email' => ['nullable', 'email:rfc', 'max:255', Rule::unique('patients')->ignore($patient->id)],
            'emergency_contact' => 'nullable|string',
            'source' => 'nullable|string|max:255|in:TikTok,Website,Referral,Walk-in',
            'geolocation' => 'nullable|string',
            'registered_by_staff_id' => 'nullable|integer|exists:staff,id',
            'corporate_client_id' => 'nullable|integer|exists:corporate_clients,id',
            'policy_id' => 'nullable|integer|exists:insurance_policies,id',
        ];
    }
}
