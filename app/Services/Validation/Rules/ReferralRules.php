<?php

namespace App\Services\Validation\Rules;

class ReferralRules
{
    public static function store(): array
    {
        return [
            'partner_id' => ['required', 'integer', 'exists:partners,id'],
            'agreement_id' => ['nullable', 'integer', 'exists:partner_agreements,id'],
            'referred_patient_id' => ['required', 'integer', 'exists:patients,id'],
            'referral_date' => ['required', 'date'],
            'status' => ['nullable', 'string', 'in:Pending,Converted,Rejected'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public static function update($model): array
    {
        return [
            'partner_id' => ['sometimes', 'integer', 'exists:partners,id'],
            'agreement_id' => ['sometimes', 'nullable', 'integer', 'exists:partner_agreements,id'],
            'referred_patient_id' => ['sometimes', 'integer', 'exists:patients,id'],
            'referral_date' => ['sometimes', 'date'],
            'status' => ['sometimes', 'nullable', 'string', 'in:Pending,Converted,Rejected'],
            'notes' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
