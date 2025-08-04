<?php

namespace App\DTOs;

class CreateLandingPageDTO
{
    public function __construct(
        public ?int $campaign_id,
        public string $name,
        public string $url,
        public ?float $conversion_rate,
        public ?string $notes
    ) {}
}
