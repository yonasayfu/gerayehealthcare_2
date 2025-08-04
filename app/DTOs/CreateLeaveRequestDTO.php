<?php

namespace App\DTOs;

class CreateLeaveRequestDTO
{
    public function __construct(
        public int $staff_id,
        public string $start_date,
        public string $end_date,
        public string $reason,
        public ?string $status,
        public ?string $admin_notes
    ) {}
}
