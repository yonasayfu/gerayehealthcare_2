<?php

namespace App\DTOs;

class CreateInventoryMaintenanceRecordDTO
{
    public function __construct(
        public int $item_id,
        public ?string $scheduled_date,
        public ?string $actual_date,
        public ?int $performed_by_staff_id,
        public ?float $cost,
        public ?string $description,
        public ?string $next_due_date,
        public ?string $status
    ) {}
}
