<?php

namespace App\Services\Validation\Rules;

class InventoryItemRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:100',
            'unit_price' => 'required|numeric|min:0',
            'quantity_in_stock' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'is_active' => 'boolean',
        ];
    }
    
    public static function update($item): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:100',
            'unit_price' => 'required|numeric|min:0',
            'quantity_in_stock' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'is_active' => 'boolean',
        ];
    }
}
