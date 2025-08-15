<?php

namespace App\DTOs;

use Spatie\LaravelData\Data;

class UpdateSupplierDTO extends Data
{
    public function __construct(
        public string $name,
        public ?string $contact_person,
        public ?string $email,
        public ?string $phone,
        public ?string $address,
    ) {}
}
