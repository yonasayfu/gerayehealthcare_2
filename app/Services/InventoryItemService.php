<?php

namespace App\Services;

use App\DTOs\CreateInventoryItemDTO;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\AdditionalExportConfigs;

class InventoryItemService extends BaseService
{
    use ExportableTrait;

    public function __construct(InventoryItem $inventoryItem)
    {
        parent::__construct($inventoryItem);
    }

    protected function applySearch($query, $search)
    {
        $query->where('name', 'like', "%{$search}%")
              ->orWhere('serial_number', 'like', "%{$search}%");
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, InventoryItem::class, AdditionalExportConfigs::getInventoryItemConfig());
    }

    public function printSingle($id, Request $request)
    {
        $inventoryItem = $this->getById($id);
        return $this->handlePrintSingle($request, $inventoryItem, AdditionalExportConfigs::getInventoryItemConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, InventoryItem::class, AdditionalExportConfigs::getInventoryItemConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, InventoryItem::class, AdditionalExportConfigs::getInventoryItemConfig());
    }
}
