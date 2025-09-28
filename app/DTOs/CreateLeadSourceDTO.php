<?php

namespace App\DTOs;

class CreateLeadSourceDTO
{
    public function __construct(
        public string $name,
        public ?string $category,
        public ?string $description,
        public bool $is_active
    ) {}
}
