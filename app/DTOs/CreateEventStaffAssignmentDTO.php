<?php

namespace App\DTOs;

class CreateEventStaffAssignmentDTO
{
    public function __construct(
        public int $event_id,
        public int $staff_id,
        public ?string $role,
        public ?string $notes
    ) {}
}
