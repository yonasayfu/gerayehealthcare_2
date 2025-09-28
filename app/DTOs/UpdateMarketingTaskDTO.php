<?php

namespace App\DTOs;

use Spatie\LaravelData\Data;

class UpdateMarketingTaskDTO extends Data
{
    public function __construct(
        public ?int $campaign_id,
        public ?int $assigned_to_staff_id,
        public ?string $title,
        public ?string $description,
        public ?string $expected_results,
        public ?string $due_date,
        public ?string $status,
        public ?string $priority,
        public ?string $completed_at
    ) {}
}
