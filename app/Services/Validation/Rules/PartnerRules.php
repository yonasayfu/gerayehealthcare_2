<?php

namespace App\Services\Validation\Rules;

class PartnerRules
{
    public static function store(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:Corporate,NGO,School,Bank,Government Agency'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:1000'],
            'engagement_status' => ['required', 'string', 'in:Prospect,Active,Inactive'],
            'account_manager_id' => ['nullable', 'integer', 'exists:staff,id'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public static function update($model): array
    {
        // Same constraints for update to prevent nulling not-null columns
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'type' => ['sometimes', 'required', 'string', 'in:Corporate,NGO,School,Bank,Government Agency'],
            'contact_person' => ['sometimes', 'nullable', 'string', 'max:255'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:50'],
            'address' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'engagement_status' => ['sometimes', 'required', 'string', 'in:Prospect,Active,Inactive'],
            'account_manager_id' => ['sometimes', 'nullable', 'integer', 'exists:staff,id'],
            'notes' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
