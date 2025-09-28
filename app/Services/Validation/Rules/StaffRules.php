<?php

namespace App\Services\Validation\Rules;

class StaffRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            // Relaxed: remove DNS check to prevent false negatives in non-public domains
            'email' => 'required|email:rfc|unique:staff,email',
            'phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^\+?[0-9\s\-]{7,20}$/',
            ],
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'hourly_rate' => 'nullable|numeric|min:0',
            'hire_date' => 'nullable|date',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^\+?[0-9\s\-]{7,20}$/',
            ],
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }

    public static function update($staff): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            // Relaxed: remove DNS check to prevent false negatives in non-public domains
            'email' => 'required|email:rfc|unique:staff,email,'.$staff->id,
            'phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^\+?[0-9\s\-]{7,20}$/',
            ],
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'hourly_rate' => 'nullable|numeric|min:0',
            'hire_date' => 'nullable|date',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^\+?[0-9\s\-]{7,20}$/',
            ],
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }
}
