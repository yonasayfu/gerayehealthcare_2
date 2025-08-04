<?php

namespace App\DTOs;

use Illuminate\Http\UploadedFile;

class CreateStaffDTO
{
    public function __construct(
        public string $first_name,
        public string $last_name,
        public string $email,
        public ?string $phone,
        public ?string $position,
        public ?string $department,
        public ?string $status,
        public ?string $hire_date,
        public ?UploadedFile $photo,
        public ?int $user_id
    ) {}
}
