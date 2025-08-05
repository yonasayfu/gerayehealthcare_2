<?php

namespace App\DTOs;

class CreatePatientDTO
{
    public function __construct(
        public string $full_name,
        public ?string $fayda_id,
        public string $date_of_birth,
        public ?string $gender,
        public ?string $address,
        public ?string $phone_number,
        public ?string $email,
        public ?string $emergency_contact,
        public ?string $source,
        public ?string $geolocation,
        public ?int $registered_by_staff_id = null
    ) {}
}
