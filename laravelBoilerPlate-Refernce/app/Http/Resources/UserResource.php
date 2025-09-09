<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'profile_photo_path' => $this->profile_photo_path,
            'profile_photo_url' => $this->profile_photo_path 
                ? asset('storage/' . $this->profile_photo_path)
                : null,
            'is_active' => $this->is_active,
            'email_verified_at' => $this->email_verified_at?->toISOString(),
            'roles' => $this->whenLoaded('roles', function () {
                return $this->roles->map(function ($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                        'display_name' => ucwords(str_replace('-', ' ', $role->name)),
                    ];
                });
            }),
            'permissions' => $this->whenLoaded('permissions', function () {
                return $this->getAllPermissions()->map(function ($permission) {
                    return [
                        'id' => $permission->id,
                        'name' => $permission->name,
                        'display_name' => ucwords(str_replace('-', ' ', $permission->name)),
                    ];
                });
            }),
            'staff' => $this->whenLoaded('staff', function () {
                return $this->staff ? [
                    'id' => $this->staff->id,
                    'first_name' => $this->staff->first_name,
                    'last_name' => $this->staff->last_name,
                    'position' => $this->staff->position,
                    'department' => $this->staff->department,
                    'hire_date' => $this->staff->hire_date,
                ] : null;
            }),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'formatted_created_date' => $this->created_at->format('M d, Y'),
        ];
    }
}
