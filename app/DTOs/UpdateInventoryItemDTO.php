<?php

namespace App\DTOs;

use Spatie\LaravelData\Data;

class UpdateInventoryItemDTO extends Data
{
    public function __construct(
        public string $name,
        public ?string $description,
        public ?string $item_category,
        public ?string $item_type,
        public ?string $serial_number,
        public ?string $purchase_date,
        public ?string $warranty_expiry,
        public ?int $supplier_id,
        public ?string $assigned_to_type,
        public ?int $assigned_to_id,
        public ?string $last_maintenance_date,
        public ?string $next_maintenance_due,
        public ?string $maintenance_schedule,
        public ?string $notes,
        public ?string $status,
        public ?int $quantity_on_hand,
        public ?int $reorder_level,
    ) {}
}
