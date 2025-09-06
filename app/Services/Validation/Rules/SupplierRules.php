<?php

namespace App\Services\Validation\Rules;

class SupplierRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:suppliers,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'contact_person_phone' => 'nullable|string|max:20',
            'contact_person_email' => 'nullable|email',
            'website' => 'nullable|url',
            'tax_id' => 'nullable|string|max:50',
        ];
    }

    public static function update($supplier): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:suppliers,email,'.$supplier->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'contact_person_phone' => 'nullable|string|max:20',
            'contact_person_email' => 'nullable|email',
            'website' => 'nullable|url',
            'tax_id' => 'nullable|string|max:50',
        ];
    }
}
