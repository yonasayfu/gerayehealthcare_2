<?php

namespace App\DTOs;

use Spatie\LaravelData\Data;

class UpdateMarketingLeadDTO extends Data
{
    public function __construct(
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
        public ?string $notes,
    ) {}
}
