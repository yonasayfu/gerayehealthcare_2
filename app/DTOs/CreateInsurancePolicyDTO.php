<?php

namespace App\DTOs;

class CreateInsurancePolicyDTO
{
    public function __construct(
        public ?int $insurance_company_id,
        public ?int $corporate_client_id,
        public string $service_type,
        public ?string $service_type_amharic,
        public ?float $coverage_percentage,
        public ?string $coverage_type,
        public ?bool $is_active,
        public ?string $notes
    ) {}
}
