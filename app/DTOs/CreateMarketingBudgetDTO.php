<?php

namespace App\DTOs;

class CreateMarketingBudgetDTO
{
    public function __construct(
        public ?int $campaign_id,
        public float $allocated_amount,
        public ?float $spent_amount,
        public ?string $start_date,
        public ?string $end_date,
        public ?string $notes
    ) {}
}
