<?php

namespace App\DTOs;

class UpdateVisitServiceDTO
{
    public function __construct(
        public ?int $patient_id = null,
        public ?int $staff_id = null,
        public ?string $scheduled_at = null,
        public ?string $check_in_time = null,
        public ?string $check_out_time = null,
        public ?float $check_in_latitude = null,
        public ?float $check_in_longitude = null,
        public ?float $check_out_latitude = null,
        public ?float $check_out_longitude = null,
        public ?string $visit_notes = null,
        public ?string $service_description = null,
        public ?\Illuminate\Http\UploadedFile $prescription_file = null,
        public ?\Illuminate\Http\UploadedFile $vitals_file = null,
        public ?string $status = null,
        public ?float $cost = null,
        public ?bool $is_paid_to_staff = null,
        public ?bool $is_invoiced = null,
        public ?int $service_id = null,
        public ?int $assignment_id = null,
        public ?int $event_id = null,
        public ?string $time_change_reason = null
    ) {}
}
