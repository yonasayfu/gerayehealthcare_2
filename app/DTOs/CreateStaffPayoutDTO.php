<?php

namespace App\DTOs;

class CreateStaffPayoutDTO
{
    public function __construct(
        public int $staff_id,
        public ?float $total_amount = null,
        public ?string $payout_date = null,
        public ?string $status = 'Completed',
        public ?string $notes = null
    ) {}
}
