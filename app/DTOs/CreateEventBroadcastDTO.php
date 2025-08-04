<?php

namespace App\DTOs;

class CreateEventBroadcastDTO
{
    public function __construct(
        public int $event_id,
        public string $broadcast_channel,
        public ?string $broadcast_date,
        public ?string $status,
        public ?string $notes
    ) {}
}
