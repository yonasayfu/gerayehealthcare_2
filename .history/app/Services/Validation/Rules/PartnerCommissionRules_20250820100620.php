<?php

namespace App\Services\Validation\Rules;

class PartnerCommissionRules
{
    public static function store(): array
    {
        return [
            'agreement_id' => 'required|exists:partner_agreements,id',
            'referral_id' => 'required|exists:referrals,id',
            'invoice_id' => 'required|exists:invoices,id',
            'commission_amount' => 'required|numeric|min:0',
            'calculation_date' => 'required|date',
            'payout_date' => 'nullable|date',
            'status' => 'nullable|string|max:50',
        ];
    }

    public static function update($model): array
    {
        return [
            'agreement_id' => 'sometimes|required|exists:partner_agreements,id',
            'referral_id' => 'sometimes|required|exists:referrals,id',
            'invoice_id' => 'sometimes|required|exists:invoices,id',
            'commission_amount' => 'sometimes|required|numeric|min:0',
            'calculation_date' => 'sometimes|required|date',
            'payout_date' => 'nullable|date',
            'status' => 'nullable|string|max:50',
        ];
    }
}
