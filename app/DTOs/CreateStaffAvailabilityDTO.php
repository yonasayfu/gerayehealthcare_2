<?php

namespace App\DTOs;

class CreateStaffAvailabilityDTO
{
    public function __construct(
        public int $staff_id,
        public string $start_time,
        public string $end_time,
        public string $status
    ) {}
}
