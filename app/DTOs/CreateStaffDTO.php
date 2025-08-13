<?php

namespace App\DTOs;

use Spatie\LaravelData\Data;
use Illuminate\Http\UploadedFile;

class CreateStaffDTO extends Data
{
    public function __construct(
        public string $first_name,
        public string $last_name,
        public string $email,
        public ?string $phone,
        public ?string $position,
        public ?string $department,
        public ?string $role,
        public ?string $status,
        public ?float $hourly_rate,
        public ?string $hire_date,
        public ?UploadedFile $photo,
        public ?int $user_id,
    ) {}
}
