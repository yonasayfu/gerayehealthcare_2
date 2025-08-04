<?php

namespace App\DTOs;

class CreateEventParticipantDTO
{
    public function __construct(
        public int $event_id,
        public int $patient_id,
        public ?string $status,
        public ?string $notes
    ) {}
}
