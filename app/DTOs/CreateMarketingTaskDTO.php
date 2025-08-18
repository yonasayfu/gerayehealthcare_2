<?php

namespace App\DTOs;

use Spatie\LaravelData\Data;

class CreateMarketingTaskDTO extends Data
{
    public function __construct(
        public int $campaign_id,
        public int $assigned_to_staff_id,
        public ?int $related_content_id,
        public ?int $doctor_id,
        public string $task_type,
        public string $title,
        public ?string $description,
        public ?string $expected_results,
        public string $scheduled_at,
        public ?string $completed_at,
        public string $status,
        public ?string $notes
    ) {}
}
