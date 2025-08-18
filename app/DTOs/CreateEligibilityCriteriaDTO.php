<?php

namespace App\DTOs;

class CreateEligibilityCriteriaDTO
{
    public function __construct(
        public int $event_id,
        public string $criteria_title,
        public string $operator,
        public ?string $value,
    ) {}
}
