<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StaffResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $fullName = trim(sprintf('%s %s', $this->first_name, $this->last_name));
        $availabilityStatus = $this->status === 'Active' ? 'available' : 'offline';
        $experienceYears = null;

        if ($this->hire_date) {
            try {
                $experienceYears = now()->diffInYears($this->hire_date);
            } catch (\Throwable $e) {
                $experienceYears = null;
            }
        }

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'phone' => $this->phone,
            'specialization' => $this->position ?? $this->department ?? 'General',
            'qualification' => null,
            'experience_years' => $experienceYears,
            'hourly_rate' => $this->hourly_rate,
            'status' => strtolower($this->status ?? 'inactive'),
            'availability_status' => $availabilityStatus,
            'work_schedule' => null,
            'location' => null,
            'department' => $this->department,
            'notes' => null,
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
            'hire_date' => optional($this->hire_date)->toDateString(),
            'photo' => $this->photo_url,
            'role' => $this->role,
            'user' => $this->user ? [
                'id' => $this->user->id,
                'name' => $fullName ?: $this->user->name,
                'email' => $this->email ?? $this->user->email,
                'email_verified_at' => optional($this->user->email_verified_at)->toDateTimeString(),
                'created_at' => optional($this->user->created_at)->toDateTimeString(),
                'updated_at' => optional($this->user->updated_at)->toDateTimeString(),
            ] : null,
            'visit_services' => [],
            'assigned_patients' => [],
        ];
    }
}
