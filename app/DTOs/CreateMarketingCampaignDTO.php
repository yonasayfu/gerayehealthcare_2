<?php

namespace App\DTOs;

class CreateMarketingCampaignDTO
{
    public function __construct(
        public string $campaign_name,
        public ?string $campaign_code,
        public ?string $utm_campaign,
        public ?int $platform_id,
        public ?string $campaign_type,
        public ?string $status,
        public ?string $start_date,
        public ?string $end_date,
        public ?string $description,
        public ?int $assigned_staff_id,
        public ?int $created_by_staff_id
    ) {}
}
