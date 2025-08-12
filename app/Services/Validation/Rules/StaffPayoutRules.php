<?php

namespace App\Services\Validation\Rules;

class StaffPayoutRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'staff_id' => 'required|exists:staff,id',
            'total_amount' => 'required|numeric|min:0',
            'payout_date' => 'required|date',
            'status' => 'required|string|in:Planned,Processing,Paid,Completed',
            'notes' => 'nullable|string',
        ];
    }

    public static function update($item): array
    {
        // Staff payouts are typically not updated via a simple form.
        // If update functionality is needed, define rules here.
        return [];
    }
}