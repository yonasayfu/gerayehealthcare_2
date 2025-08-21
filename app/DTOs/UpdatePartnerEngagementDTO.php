<?php

namespace App\DTOs;

use Spatie\LaravelData\Data;

class UpdatePartnerEngagementDTO extends Data
{
    public ?int $partner_id;

    public ?int $staff_id;

    public ?string $engagement_type;

    public ?string $summary;

    public ?string $engagement_date;

    public ?string $follow_up_date;
}
