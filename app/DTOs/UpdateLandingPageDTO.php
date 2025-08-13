<?php

namespace App\DTOs;

use Spatie\LaravelData\Data;

class UpdateLandingPageDTO extends Data
{
    public function __construct(
        public ?int $campaign_id,
        public ?string $name,
        public ?string $url,
        public ?float $conversion_rate,
        public ?string $notes,
        public ?array $form_fields = []
    ) {}
}
