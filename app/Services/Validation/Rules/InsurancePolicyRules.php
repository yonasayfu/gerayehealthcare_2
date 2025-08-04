<?php

namespace App\Services\Validation\Rules;

class InsurancePolicyRules
{
    public static function store(): array
    {
        return [
            'insurance_company_id' => 'nullable|integer|exists:insurance_companies,id',
            'corporate_client_id' => 'nullable|integer|exists:corporate_clients,id',
            'service_type' => 'required|string|max:255',
            'service_type_amharic' => 'nullable|string|max:255',
            'coverage_percentage' => 'nullable|numeric|min:0|max:100',
            'coverage_type' => 'nullable|string|in:Full,Partial,Copay',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ];
    }

    public static function update(): array
    {
        return [
            'insurance_company_id' => 'nullable|integer|exists:insurance_companies,id',
            'corporate_client_id' => 'nullable|integer|exists:corporate_clients,id',
            'service_type' => 'sometimes|required|string|max:255',
            'service_type_amharic' => 'nullable|string|max:255',
            'coverage_percentage' => 'nullable|numeric|min:0|max:100',
            'coverage_type' => 'nullable|string|in:Full,Partial,Copay',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ];
    }
}