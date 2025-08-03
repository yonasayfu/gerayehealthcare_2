<?php

namespace App\Services\Validation\Rules;

class InventoryTransactionRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'item_id' => 'required|exists:inventory_items,id',
            'request_id' => 'nullable|exists:inventory_requests,id',
            'from_location' => 'nullable|string|max:255',
            'to_location' => 'nullable|string|max:255',
            'from_assigned_to_type' => 'nullable|string|max:50',
            'from_assigned_to_id' => 'nullable|integer',
            'to_assigned_to_type' => 'nullable|string|max:50',
            'to_assigned_to_id' => 'nullable|integer',
            'quantity' => 'required|integer',
            'transaction_type' => 'required|string|max:50',
            'performed_by_id' => 'required|exists:staff,id',
            'notes' => 'nullable|string',
        ];
    }

    public static function update($item): array
    {
        // Inventory transactions are typically not updated, only created.
        // If update functionality is needed, define rules here.
        return [];
    }
}