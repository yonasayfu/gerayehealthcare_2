<?php

namespace App\Services\Validation\Rules;

class CorporateClientRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'company_name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:corporate_clients,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }
    
    public static function update($client): array
    {
        return [
            'company_name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:corporate_clients,email,' . $client->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }
}
