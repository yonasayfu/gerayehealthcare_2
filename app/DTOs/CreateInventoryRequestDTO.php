<?php

namespace App\DTOs;

use Spatie\LaravelData\Data;

class CreateInventoryRequestDTO extends Data
{
    public function __construct(
        public int $requester_id,
        public int $item_id,
        public int $quantity_requested,
        public ?string $reason,
        public string $status = 'Pending',
        public string $priority,
        public ?string $needed_by_date = null
    ) {}
}