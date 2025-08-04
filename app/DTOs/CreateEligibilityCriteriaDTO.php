<?php

namespace App\DTOs;

class CreateEligibilityCriteriaDTO
{
    public function __construct(
        public string $name,
        public ?string $description,
        public ?string $criteria_type,
        public ?string $value,
        public ?bool $is_active
    ) {}
}
