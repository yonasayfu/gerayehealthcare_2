<?php

namespace App\DTOs;

class UpdateEventRecommendationDTO
{
    public function __construct(
        public ?int $event_id,
        public ?int $patient_id,
        public ?string $recommendation_notes,
        public ?string $status,
        public ?string $patient_name,
        public ?string $patient_phone
    ) {}
}
