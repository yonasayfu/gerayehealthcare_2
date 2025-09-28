<?php

namespace App\DTOs;

class CreateCampaignContentDTO
{
    public function __construct(
        public int $campaign_id,
        public string $content_type,
        public ?string $content_url,
        public ?string $description
    ) {}
}
