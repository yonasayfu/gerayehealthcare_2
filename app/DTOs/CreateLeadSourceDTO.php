<?php

namespace App\DTOs;

class CreateLeadSourceDTO
{
    public function __construct(
        public string $name,
        public ?string $description
    ) {}
}
