<?php

namespace App\Services\Validation\Rules;

class InsurancePolicyRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'insurance_company_id' => 'required|exists:insurance_companies,id',
            'policy_number' => 'required|string|max:255|unique:insurance_policies,policy_number',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'coverage_amount' => 'required|numeric|min:0',
            'premium_amount' => 'required|numeric|min:0',
            'status' => 'required|string|in:Active,Expired,Cancelled',
        ];
    }
    
    public static function update($policy): array
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'insurance_company_id' => 'required|exists:insurance_companies,id',
            'policy_number' => 'required|string|max:255|unique:insurance_policies,policy_number,' . $policy->id,
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'coverage_amount' => 'required|numeric|min:0',
            'premium_amount' => 'required|numeric|min:0',
            'status' => 'required|string|in:Active,Expired,Cancelled',
        ];
    }
}
