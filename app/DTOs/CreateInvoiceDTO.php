<?php

namespace App\DTOs;

class CreateInvoiceDTO extends BaseDTO
{
    public function __construct(
        public int $patient_id,
        public string $invoice_date,
        public string $due_date,
        public array $visit_ids
    ) {}
}
