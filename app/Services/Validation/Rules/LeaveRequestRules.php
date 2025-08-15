<?php

namespace App\Services\Validation\Rules;

class LeaveRequestRules extends BaseResourceRules
{
    public static function store(): array
    {
        // Leave requests are typically created by staff, not admin.
        // If admin creation is needed, define rules here.
        return [];
    }

    public static function update($item): array
    {
        return [
            'status' => 'required|in:Pending,Approved,Denied',
            'admin_notes' => 'nullable|string|max:1000',
        ];
    }
}