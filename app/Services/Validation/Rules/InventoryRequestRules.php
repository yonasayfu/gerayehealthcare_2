<?php

namespace App\Services\Validation\Rules;

class InventoryRequestRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'requester_id' => 'required|exists:staff,id',
            'item_id' => 'required|exists:inventory_items,id',
            'quantity_requested' => 'required|integer|min:1',
            'reason' => 'nullable|string',
            'priority' => 'required|string|in:Low,Normal,High,Urgent',
            'needed_by_date' => 'nullable|date',
        ];
    }

    public static function update($item): array
    {
        return [
            'requester_id' => 'required|exists:staff,id',
            'approver_id' => 'nullable|exists:staff,id',
            'item_id' => 'required|exists:inventory_items,id',
            'quantity_requested' => 'required|integer|min:1',
            'quantity_approved' => 'nullable|integer|min:0|lte:quantity_requested',
            'reason' => 'nullable|string',
            'status' => 'required|string|in:Pending,Approved,Rejected,Fulfilled,Partially Fulfilled',
            'priority' => 'required|string|in:Low,Normal,High,Urgent',
            'needed_by_date' => 'nullable|date',
            'approved_at' => 'nullable|date',
            'fulfilled_at' => 'nullable|date',
        ];
    }
}
