<?php

namespace App\DTOs;

use Spatie\LaravelData\Data;

class CreateLeaveRequestDTO extends Data
{
    public function __construct(
        public int $staff_id,
        public string $start_date,
        public string $end_date,
        public string $type,
        public ?string $reason,
        public ?string $status,
        public ?string $admin_notes,
    ) {}
}
