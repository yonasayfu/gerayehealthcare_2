<?php

namespace App\DTOs;

class CreateEmployeeInsuranceRecordDTO
{
    public function __construct(
        public int $patient_id,
        public int $policy_id,
        public ?string $kebele_id,
        public ?string $woreda,
        public ?string $region,
        public ?string $federal_id,
        public ?string $ministry_department,
        public ?string $employee_id_number,
        public ?bool $verified,
        public ?string $verified_at
    ) {}
}
