<?php

namespace App\DTOs;

class CreateServiceDTO
{
    public function __construct(
        public string $name,
        public ?string $description,
        public float $price,
        public ?int $duration,
        public ?bool $is_active
    ) {}
}
