<?php

namespace App\DTOs;

class CreateTaskDelegationDTO
{
    public function __construct(
        public string $title,
        public int $assigned_to,
        public string $due_date,
        public ?string $status,
        public ?string $notes
    ) {}
}
