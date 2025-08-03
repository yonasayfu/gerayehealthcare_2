<?php

namespace App\Services\Validation\Rules;

class InventoryAlertRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'item_id' => 'nullable|exists:inventory_items,id',
            'alert_type' => 'required|string|max:255',
            'threshold_value' => 'nullable|string|max:255',
            'message' => 'required|string',
            'is_active' => 'boolean',
        ];
    }

    public static function update($item): array
    {
        return self::store();
    }
}