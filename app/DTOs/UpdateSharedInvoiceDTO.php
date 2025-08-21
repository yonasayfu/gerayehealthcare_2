<?php

namespace App\DTOs;

class UpdateSharedInvoiceDTO
{
    public function __construct(
        public readonly ?int $invoice_id,
        public readonly ?int $partner_id,
        public readonly ?string $share_date,
        public readonly ?string $status,
        public readonly ?string $notes
    ) {}
}
