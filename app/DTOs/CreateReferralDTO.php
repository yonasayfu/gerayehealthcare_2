<?php

namespace App\DTOs;

use Spatie\LaravelData\Data;

class CreateReferralDTO extends Data
{
    public int $partner_id;

    public ?int $agreement_id;

    public int $referred_patient_id;

    public string $referral_date;

    public ?string $status;

    public ?string $notes;
}
