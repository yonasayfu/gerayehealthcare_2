<?php

namespace App\DTOs;

class CreateInsuranceCompanyDTO
{
    public function __construct(
        public string $name,
        public ?string $name_amharic,
        public ?string $contact_person,
        public ?string $contact_email,
        public ?string $contact_phone,
        public ?string $address,
        public ?string $address_amharic
    ) {}
}
