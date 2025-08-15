<?php

namespace App\DTOs;

use Spatie\LaravelData\Data;

class UpdateLeadSourceDTO extends Data
{
    public function __construct(
        public string $name,
        public ?string $category,
        public ?string $description,
        public ?bool $is_active,
    ) {}
}
