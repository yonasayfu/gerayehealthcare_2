<?php

namespace App\DTOs;

class CreateVisitServiceDTO
{
    public function __construct(
        public int $patient_id,
        public ?int $staff_id,
        public string $scheduled_at,
        public ?string $check_in_time,
        public ?string $check_out_time,
        public ?string $visit_notes,
        public ?string $prescription_file,
        public ?string $vitals_file,
        public ?string $status,
        public ?float $cost,
        public ?bool $is_paid_to_staff,
        public ?bool $is_invoiced,
        public ?int $service_id,
        public ?int $assignment_id,
        public ?int $event_id
    ) {}
}
