<?php

namespace App\DTOs;

use Spatie\LaravelData\Data;

class UpdatePartnerCommissionDTO extends Data
{
    public ?int $agreement_id;

    public ?int $referral_id;

    public ?int $invoice_id;

    public ?float $commission_amount;

    public ?string $calculation_date;

    public ?string $payout_date;

    public ?string $status;
}
