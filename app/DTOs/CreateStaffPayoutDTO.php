<?php

namespace App\DTOs;

class CreateStaffPayoutDTO
{
    public function __construct(
        public int $staff_id,
        public float $total_amount,
        public string $payout_date,
        public ?string $status,
        public ?string $notes
    ) {}
}
