<?php

namespace App\Services\Validation\Rules;

class InventoryMaintenanceRecordRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'item_id' => 'required|exists:inventory_items,id',
            'scheduled_date' => 'nullable|date',
            'actual_date' => 'nullable|date|after_or_equal:scheduled_date',
            'performed_by_staff_id' => 'nullable|exists:staff,id',
            'cost' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'next_due_date' => 'nullable|date|after_or_equal:actual_date',
            'status' => 'required|string|in:Scheduled,Completed,Overdue,Cancelled',
        ];
    }

    public static function update($item): array
    {
        return self::store();
    }
}
