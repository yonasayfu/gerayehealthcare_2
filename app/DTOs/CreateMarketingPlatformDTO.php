<?php

namespace App\DTOs;

class CreateMarketingPlatformDTO
{
    public function __construct(
        public string $name,
        public ?string $description,
        public ?string $api_key
    ) {}
}
