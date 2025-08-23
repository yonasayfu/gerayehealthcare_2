<?php

namespace App\DTOs;

class CreateEventBroadcastDTO extends BaseDTO
{
    public function __construct(
        public int $event_id,
        public string $channel,
        public string $message,
        public int $sent_by_staff_id
    ) {}
}
