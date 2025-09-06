<?php

namespace App\Services\Validation\Rules;

class InventoryItemRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'item_category' => 'nullable|string|max:100',
            'item_type' => 'nullable|string|max:100',
            'serial_number' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'warranty_expiry' => 'nullable|date|after_or_equal:purchase_date',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'assigned_to_type' => 'nullable|in:staff,patient,department,event',
            'assigned_to_id' => 'nullable|integer',
            'last_maintenance_date' => 'nullable|date',
            'next_maintenance_due' => 'nullable|date|after_or_equal:last_maintenance_date',
            'maintenance_schedule' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'required|string|in:Available,In Use,Under Maintenance,Lost,Damaged,Retired',
            // Optional inventory management fields if present in UI/model
            'quantity_on_hand' => 'nullable|integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',
        ];
    }

    public static function update($item): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'item_category' => 'nullable|string|max:100',
            'item_type' => 'nullable|string|max:100',
            'serial_number' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'warranty_expiry' => 'nullable|date|after_or_equal:purchase_date',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'assigned_to_type' => 'nullable|in:staff,patient,department,event',
            'assigned_to_id' => 'nullable|integer',
            'last_maintenance_date' => 'nullable|date',
            'next_maintenance_due' => 'nullable|date|after_or_equal:last_maintenance_date',
            'maintenance_schedule' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'required|string|in:Available,In Use,Under Maintenance,Lost,Damaged,Retired',
            'quantity_on_hand' => 'nullable|integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',
        ];
    }
}
