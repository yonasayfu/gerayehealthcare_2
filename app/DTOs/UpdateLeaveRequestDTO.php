<?php

namespace App\DTOs;

class UpdateLeaveRequestDTO
{
    public function __construct(
        public ?string $status = null,
        public ?string $admin_notes = null
    ) {}
}