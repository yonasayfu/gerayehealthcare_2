<?php

namespace App\DTOs;

class CreateCorporateClientDTO
{
    public function __construct(
        public string $organization_name,
        public ?string $organization_name_amharic,
        public ?string $contact_person,
        public ?string $contact_email,
        public ?string $contact_phone,
        public ?string $tin_number,
        public ?string $trade_license_number,
        public ?string $address
    ) {}
}
