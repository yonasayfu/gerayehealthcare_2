<?php

namespace App\Services\Validation\Rules;

class CorporateClientRules
{
    public static function store(): array
    {
        return [
            'organization_name' => 'required|string|max:255',
            'organization_name_amharic' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:255',
            'tin_number' => 'nullable|string|max:50',
            'trade_license_number' => 'nullable|string|max:100',
            'address' => 'nullable|string',
        ];
    }

    public static function update(): array
    {
        return [
            'organization_name' => 'sometimes|required|string|max:255',
            'organization_name_amharic' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:255',
            'tin_number' => 'nullable|string|max:50',
            'trade_license_number' => 'nullable|string|max:100',
            'address' => 'nullable|string',
        ];
    }
}