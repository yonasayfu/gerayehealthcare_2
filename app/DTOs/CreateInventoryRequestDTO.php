<?php

namespace App\DTOs;

class CreateInventoryRequestDTO
{
    public function __construct(
        public int $requester_id,
        public ?int $approver_id,
        public int $item_id,
        public int $quantity_requested,
        public ?int $quantity_approved,
        public ?string $reason,
        public ?string $status,
        public ?string $priority,
        public ?string $needed_by_date
    ) {}
}
