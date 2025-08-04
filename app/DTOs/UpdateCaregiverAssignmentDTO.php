<?php

namespace App\DTOs;

class UpdateCaregiverAssignmentDTO
{
    public function __construct(
        public ?int $staff_id,
        public ?int $patient_id,
        public ?string $shift_start,
        public ?string $shift_end,
        public ?string $status
    ) {}
}
