<?php

namespace App\DTOs;

class CreateSharedInvoiceDTO
{
    public function __construct(
        public readonly int $invoice_id,
        public readonly int $partner_id,
        public readonly ?int $shared_by_staff_id,
        public readonly string $share_date,
        public readonly string $status,
        public readonly ?string $notes
    ) {}
}
