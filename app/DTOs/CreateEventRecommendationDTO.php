<?php

namespace App\DTOs;

class CreateEventRecommendationDTO
{
    public function __construct(
        public int $event_id,
        public string $source_channel,
        public ?string $recommended_by_name,
        public ?string $recommended_by_phone,
        public string $patient_name,
        public ?string $phone_number,
        public ?string $notes,
        public string $status,
    ) {}
}