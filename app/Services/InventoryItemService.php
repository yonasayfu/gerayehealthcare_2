<?php

namespace App\Services;

use App\DTOs\CreateInventoryItemDTO;
use App\Models\InventoryItem;
use Illuminate\Http\Request;

class InventoryItemService extends BaseService
{
    public function __construct(InventoryItem $inventoryItem)
    {
        parent::__construct($inventoryItem);
    }

    protected function applySearch($query, $search)
    {
        $query->where('name', 'like', "%{$search}%")
              ->orWhere('serial_number', 'like', "%{$search}%");
    }
}
