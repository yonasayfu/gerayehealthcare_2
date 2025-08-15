<?php

namespace App\DTOs;

use Spatie\LaravelData\Data;

class CreateInventoryAlertDTO extends Data
{
    public function __construct(
        public ?int $item_id,
        public string $alert_type,
        public ?string $threshold_value,
        public string $message,
        public ?bool $is_active,
        public ?string $triggered_at,
        public ?string $due_date
    ) {}
}
