<?php

namespace App\DTOs;

use Spatie\LaravelData\Data;

class CreateMarketingPlatformDTO extends Data
{
    public function __construct(
        public string $name,
        public ?string $api_endpoint,
        public ?string $api_credentials,
        public ?bool $is_active,
    ) {}
}
