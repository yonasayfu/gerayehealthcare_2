<?php

namespace App\DTOs;

class CreateVisitServiceDTO extends BaseDTO
{
    public function __construct(
        public int $patient_id,
        public ?int $staff_id,
        public string $scheduled_at,
        public ?string $check_in_time,
        public ?string $check_out_time,
        public ?string $visit_notes,
        public ?string $service_description,
        public ?\Illuminate\Http\UploadedFile $prescription_file,
        public ?\Illuminate\Http\UploadedFile $vitals_file,
        public ?string $status,
        public ?float $cost,
        public ?bool $is_paid_to_staff,
        public ?bool $is_invoiced,
        public ?int $service_id,
        public ?int $assignment_id,
        public ?int $event_id
    ) {}
}
