<?php

namespace App\Services\Validation\Rules;

class InsuranceClaimRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'insurance_policy_id' => 'required|exists:insurance_policies,id',
            'service_id' => 'required|exists:services,id',
            'claim_date' => 'required|date',
            'amount_claimed' => 'required|numeric|min:0',
            'amount_approved' => 'nullable|numeric|min:0',
            'status' => 'required|string|in:Submitted,Processing,Approved,Rejected',
            'notes' => 'nullable|string',
        ];
    }
    
    public static function update($claim): array
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'insurance_policy_id' => 'required|exists:insurance_policies,id',
            'service_id' => 'required|exists:services,id',
            'claim_date' => 'required|date',
            'amount_claimed' => 'required|numeric|min:0',
            'amount_approved' => 'nullable|numeric|min:0',
            'status' => 'required|string|in:Submitted,Processing,Approved,Rejected',
            'notes' => 'nullable|string',
        ];
    }
}
