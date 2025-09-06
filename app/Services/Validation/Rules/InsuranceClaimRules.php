<?php

namespace App\Services\Validation\Rules;

class InsuranceClaimRules
{
    public static function store(): array
    {
        return [
            'patient_id' => 'required|integer|exists:patients,id',
            'invoice_id' => 'nullable|integer|exists:invoices,id',
            'insurance_company_id' => 'nullable|integer|exists:insurance_companies,id',
            'policy_id' => 'nullable|integer|exists:insurance_policies,id',
            'claim_status' => 'nullable|string|in:Submitted,Approved,Rejected,Paid',
            'coverage_amount' => 'nullable|numeric',
            'paid_amount' => 'nullable|numeric',
            'submitted_at' => 'nullable|date',
            'processed_at' => 'nullable|date',
            'payment_due_date' => 'nullable|date',
            'payment_received_at' => 'nullable|date',
            'payment_method' => 'nullable|string|max:50',
            'reimbursement_required' => 'boolean',
            'receipt_number' => 'nullable|string|max:100',
            'is_pre_authorized' => 'boolean',
            'pre_authorization_code' => 'nullable|string|max:100',
            'denial_reason' => 'nullable|string',
            'translated_notes' => 'nullable|string',
        ];
    }

    public static function update(): array
    {
        return [
            'patient_id' => 'sometimes|required|integer|exists:patients,id',
            'invoice_id' => 'nullable|integer|exists:invoices,id',
            'insurance_company_id' => 'nullable|integer|exists:insurance_companies,id',
            'policy_id' => 'nullable|integer|exists:insurance_policies,id',
            'claim_status' => 'nullable|string|in:Submitted,Approved,Rejected,Paid',
            'coverage_amount' => 'nullable|numeric',
            'paid_amount' => 'nullable|numeric',
            'submitted_at' => 'nullable|date',
            'processed_at' => 'nullable|date',
            'payment_due_date' => 'nullable|date',
            'payment_received_at' => 'nullable|date',
            'payment_method' => 'nullable|string|max:50',
            'reimbursement_required' => 'boolean',
            'receipt_number' => 'nullable|string|max:100',
            'is_pre_authorized' => 'boolean',
            'pre_authorization_code' => 'nullable|string|max:100',
            'denial_reason' => 'nullable|string',
            'translated_notes' => 'nullable|string',
        ];
    }
}
