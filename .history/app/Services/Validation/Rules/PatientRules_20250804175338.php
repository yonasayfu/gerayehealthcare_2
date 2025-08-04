<?php

namespace App\Services\Validation\Rules;

class PatientRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:patients,email',
            'phone_number' => 'nullable|string|max:20',
            'date_of_birth' => 'required|date',
            'gender' => 'required|string|in:Male,Female,Other',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:255',
            'geolocation' => 'nullable|string',
            'registered_by_staff_id' => 'nullable|integer',
            'allergies' => 'nullable|string',
            'current_medications' => 'nullable|string',
        ];
    }
    
    public static function update($patient): array
    {
        return [
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:patients,email,' . $patient->id,
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'required|date',
            'gender' => 'required|string|in:Male,Female,Other',
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'medical_history' => 'nullable|string',
            'allergies' => 'nullable|string',
            'current_medications' => 'nullable|string',
        ];
    }
}
