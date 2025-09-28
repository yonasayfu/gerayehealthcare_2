<?php

namespace App\Services\Validation\Rules;

class StaffPayoutRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'staff_id' => 'required|exists:staff,id',
            // Computed by service from unpaid visits
            'total_amount' => 'nullable',
            'payout_date' => 'nullable',
            'status' => 'nullable',
            'notes' => 'nullable|string',
        ];
    }

    public static function update($item): array
    {
        return [
            'status' => 'nullable|string|in:Pending,Completed,Voided',
            'notes' => 'nullable|string',
        ];
    }
}
