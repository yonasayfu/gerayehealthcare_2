<?php

namespace App\DTOs;

use Spatie\DataTransferObject\DataTransferObject;
use Illuminate\Http\UploadedFile;

class CreateStaffDTO extends DataTransferObject
{
    public string $first_name;
    public string $last_name;
    public string $email;
    public string $phone;
    public string $position;
    public string $department;
    public string $role;
    public string $status;
    public string $hire_date;
    public ?UploadedFile $photo;
    public int $user_id;
    public float $hourly_rate;
}