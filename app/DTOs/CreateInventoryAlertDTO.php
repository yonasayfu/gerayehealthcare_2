<?php

namespace App\DTOs;

class CreateInventoryAlertDTO
{
    public function __construct(
        public ?int $item_id,
        public string $alert_type,
        public ?string $threshold_value,
        public string $message,
        public ?bool $is_active,
        public ?string $triggered_at
    ) {}
}
