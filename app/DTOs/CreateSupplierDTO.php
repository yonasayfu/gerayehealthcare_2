<?php

namespace App\DTOs;

class CreateSupplierDTO
{
    public function __construct(
        public string $name,
        public ?string $contact_person,
        public ?string $email,
        public ?string $phone,
        public ?string $address
    ) {}
}
