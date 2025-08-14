<?php

namespace App\DTOs;

class UpdateStaffDTO
{
    public ?string $first_name;
    public ?string $last_name;
    public ?string $email;
    public ?string $phone;
    public ?string $position;
    public ?string $department;
    public ?string $role;
    public ?string $status;
    public $hourly_rate; // normalized in service
    public ?string $hire_date;
    public $photo; // UploadedFile|string|null
    public ?int $user_id;

    public function __construct(array $data)
    {
        $this->first_name = $data['first_name'] ?? null;
        $this->last_name = $data['last_name'] ?? null;
        $this->email = $data['email'] ?? null;
        $this->phone = $data['phone'] ?? null;
        $this->position = $data['position'] ?? null;
        $this->department = $data['department'] ?? null;
        $this->role = $data['role'] ?? null;
        $this->status = $data['status'] ?? null;
        $this->hourly_rate = $data['hourly_rate'] ?? null;
        $this->hire_date = $data['hire_date'] ?? null;
        $this->photo = $data['photo'] ?? null;
        $this->user_id = $data['user_id'] ?? null;
    }

    public function toArray(): array
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'position' => $this->position,
            'department' => $this->department,
            'role' => $this->role,
            'status' => $this->status,
            'hourly_rate' => $this->hourly_rate,
            'hire_date' => $this->hire_date,
            'photo' => $this->photo,
            'user_id' => $this->user_id,
        ];
    }
}