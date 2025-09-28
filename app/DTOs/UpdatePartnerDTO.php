<?php

namespace App\DTOs;

use Spatie\LaravelData\Data;

class UpdatePartnerDTO extends Data
{
    public ?string $name;

    public ?string $type;

    public ?string $contact_person;

    public ?string $email;

    public ?string $phone;

    public ?string $address;

    public ?string $engagement_status;

    public ?int $account_manager_id;

    public ?string $notes;
}
