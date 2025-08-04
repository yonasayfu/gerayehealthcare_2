<?php

namespace App\Services\Validation\Rules;

class InsuranceCompanyRules
{
    public static function store(): array
    {
        return [
            'name' => 'required|string|max:255',
            'name_amharic' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'address_amharic' => 'nullable|string',
        ];
    }

    public static function update(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'name_amharic' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'address_amharic' => 'nullable|string',
        ];
    }
}