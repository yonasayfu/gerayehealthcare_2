<?php

namespace App\Services\Validation\Rules;

class LeaveRequestRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
        ];
    }

    public static function update($item): array
    {
        return [
            'status' => 'required|in:Pending,Approved,Denied',
            'admin_notes' => 'nullable|string|max:1000',
        ];
    }
}