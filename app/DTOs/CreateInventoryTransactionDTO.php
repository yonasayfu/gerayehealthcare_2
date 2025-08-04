<?php

namespace App\DTOs;

class CreateInventoryTransactionDTO
{
    public function __construct(
        public int $item_id,
        public ?int $request_id,
        public ?string $from_location,
        public ?string $to_location,
        public ?string $from_assigned_to_type,
        public ?int $from_assigned_to_id,
        public ?string $to_assigned_to_type,
        public ?int $to_assigned_to_id,
        public int $quantity,
        public string $transaction_type,
        public int $performed_by_id,
        public ?string $notes
    ) {}
}
