<?php

namespace App\DTOs;

class CreateLandingPageDTO
{
    public function __construct(
        public ?int $campaign_id = null,
        public ?string $page_title = null,
        public ?string $page_url = null,
        public ?string $template_used = null,
        public ?string $language = null,
        public ?array $form_fields = [],
        public ?string $conversion_goal = null,
        public ?int $views = null,
        public ?int $submissions = null,
        public ?float $conversion_rate = null,
        public ?bool $is_active = null,
        public ?string $notes = null
    ) {}
}
