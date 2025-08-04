<?php

namespace App\DTOs;

class CreateInsuranceClaimDTO
{
    public function __construct(
        public int $patient_id,
        public ?int $invoice_id,
        public ?int $insurance_company_id,
        public ?int $policy_id,
        public ?string $claim_status,
        public ?float $coverage_amount,
        public ?float $paid_amount,
        public ?string $submitted_at,
        public ?string $processed_at,
        public ?string $payment_due_date,
        public ?string $payment_received_at,
        public ?string $payment_method,
        public ?bool $reimbursement_required,
        public ?string $receipt_number,
        public ?bool $is_pre_authorized,
        public ?string $pre_authorization_code,
        public ?string $denial_reason,
        public ?string $translated_notes
    ) {}
}
