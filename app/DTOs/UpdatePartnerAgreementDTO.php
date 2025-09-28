<?php

namespace App\DTOs;

use Spatie\LaravelData\Data;

class UpdatePartnerAgreementDTO extends Data
{
    public ?int $partner_id;

    public ?string $agreement_title;

    public ?string $agreement_type;

    public ?string $status;

    public ?string $start_date;

    public ?string $end_date;

    public ?string $priority_service_level;

    public ?string $commission_type;

    public ?float $commission_rate;

    public ?string $terms_document_path;

    public ?int $signed_by_staff_id;
}
