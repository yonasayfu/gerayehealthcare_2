<?php

namespace App\DTOs;

use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Data;

class UpdateStaffDTO extends Data
{
    public function __construct(
        public readonly ?string $first_name,
        public readonly ?string $last_name,
        public readonly ?string $email,
        public readonly ?string $phone,
        public readonly ?string $position,
        public readonly ?string $department,
        public readonly ?string $role,
        public readonly ?string $status,
        public readonly ?float $hourly_rate,
        public readonly ?string $hire_date,
        public readonly UploadedFile|string|null $photo,
        public readonly ?int $user_id,
    ) {}
}
