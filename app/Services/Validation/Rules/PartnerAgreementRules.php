<?php

namespace App\Services\Validation\Rules;

class PartnerAgreementRules
{
    public static function store(): array
    {
        return [
            'partner_id' => ['required', 'integer', 'exists:partners,id'],
            'agreement_title' => ['required', 'string', 'max:255'],
            'agreement_type' => ['required', 'string', 'max:100'],
            'status' => ['required', 'string', 'in:Draft,Active,Expired,Terminated'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'priority_service_level' => ['nullable', 'string', 'in:Standard,Preferred,Premium'],
            'commission_type' => ['nullable', 'string', 'in:Percentage,FixedAmountPerPatient'],
            'commission_rate' => ['nullable', 'numeric', 'min:0'],
            'terms_document_path' => ['nullable', 'string', 'max:1024'],
            'signed_by_staff_id' => ['nullable', 'integer', 'exists:staff,id'],
        ];
    }

    public static function update($model): array
    {
        return [
            'partner_id' => ['sometimes', 'required', 'integer', 'exists:partners,id'],
            'agreement_title' => ['sometimes', 'required', 'string', 'max:255'],
            'agreement_type' => ['sometimes', 'required', 'string', 'max:100'],
            'status' => ['sometimes', 'required', 'string', 'in:Draft,Active,Expired,Terminated'],
            'start_date' => ['sometimes', 'required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'priority_service_level' => ['nullable', 'string', 'in:Standard,Preferred,Premium'],
            'commission_type' => ['nullable', 'string', 'in:Percentage,FixedAmountPerPatient'],
            'commission_rate' => ['nullable', 'numeric', 'min:0'],
            'terms_document_path' => ['nullable', 'string', 'max:1024'],
            'signed_by_staff_id' => ['nullable', 'integer', 'exists:staff,id'],
        ];
    }
}
