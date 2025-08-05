<?php

namespace App\DTOs;

class CreateServiceDTO
{
    public function __construct(
        public string $name,
        public ?string $description,
        public string $category, // Add missing field
        public int $duration, // Make required
        public float $price,
        public bool $is_active = true // Make required with default
    ) {}
}
