<?php

namespace App\DTOs;

class CreateEventDTO extends BaseDTO
{
    public function __construct(
        public string $title,
        public string $event_date,
        public ?string $description,
        public ?bool $is_free_service,
        public ?string $broadcast_status,
        public ?bool $is_pagume_campaign,
        public ?string $location,
        public ?string $region,
        public ?string $woreda,
        public ?int $created_by_staff_id
    ) {}
}
