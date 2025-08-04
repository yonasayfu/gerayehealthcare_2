<?php

namespace App\DTOs;

class CreateMarketingTaskDTO
{
    public function __construct(
        public ?int $campaign_id,
        public ?int $assigned_to_staff_id,
        public string $title,
        public ?string $description,
        public ?string $due_date,
        public ?string $status,
        public ?string $priority,
        public ?string $completed_at
    ) {}
}
