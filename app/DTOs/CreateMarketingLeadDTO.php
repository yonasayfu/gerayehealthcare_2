<?php

namespace App\DTOs;

class CreateMarketingLeadDTO
{
    public function __construct(
        public ?string $lead_code,
        public ?int $source_campaign_id,
        public string $first_name,
        public string $last_name,
        public ?string $email,
        public ?string $phone,
        public ?string $country,
        public ?string $utm_source,
        public ?string $utm_campaign,
        public ?string $utm_medium,
        public ?int $landing_page_id,
        public ?int $lead_score,
        public ?string $status,
        public ?int $assigned_staff_id,
        public ?int $converted_patient_id,
        public ?string $conversion_date,
        public ?string $notes
    ) {}
}
