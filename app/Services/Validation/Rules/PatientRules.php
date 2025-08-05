<?php

namespace App\Services\Validation\Rules;

class PatientRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'full_name' => 'required|string|max:255',
            'fayda_id' => 'nullable|string|max:255|unique:patients,fayda_id',
            'date_of_birth' => 'required|date',
            'gender' => 'nullable|string|max:10',
            'address' => 'nullable|string',
            'phone_number' => 'nullable|string|regex:/^[0-9+\-\s\(\)]+$/|max:255',
            'email' => 'nullable|email:rfc,dns|max:255|unique:patients,email',
            'emergency_contact' => 'nullable|string',
            'source' => 'nullable|string|max:255|in:TikTok,Website,Referral,Walk-in',
            'geolocation' => 'nullable|string',
            'registered_by_staff_id' => 'nullable|integer|exists:staff,id',
        ];
    }
    
    public static function update($patient): array
    {
        return [
            'full_name' => 'required|string|max:255',
            'fayda_id' => ['nullable', 'string', 'max:255', \Illuminate\Validation\Rule::unique('patients')->ignore($patient->id)],
            'date_of_birth' => 'required|date',
            'gender' => 'nullable|string|max:10',
            'address' => 'nullable|string',
            'phone_number' => 'nullable|string|regex:/^[0-9+\-\s\(\)]+$/|max:255',
            'email' => ['nullable', 'email:rfc,dns', 'max:255', \Illuminate\Validation\Rule::unique('patients')->ignore($patient->id)],
            'emergency_contact' => 'nullable|string',
            'source' => 'nullable|string|max:255|in:TikTok,Website,Referral,Walk-in',
            'geolocation' => 'nullable|string',
            'registered_by_staff_id' => 'nullable|integer|exists:staff,id',
        ];
    }
}
