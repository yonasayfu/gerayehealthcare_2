<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\InventoryItemService;
use App\Models\InventoryItem;
use App\Services\Validation\Rules\InventoryItemRules;

class InventoryItemController extends BaseController
{
    public function __construct(InventoryItemService $inventoryItemService)
    {
        parent::__construct(
            $inventoryItemService,
            InventoryItemRules::class,
            'Admin/InventoryItems',
            'inventoryItems',
            InventoryItem::class
        );
    }

    public function show(InventoryItem $inventoryItem)
    {
        return parent::show($inventoryItem->id);
    }

    public function edit(InventoryItem $inventoryItem)
    {
        return parent::edit($inventoryItem->id);
    }

    public function update(Request $request, InventoryItem $inventoryItem)
    {
        return parent::update($request, $inventoryItem->id);
    }

    public function destroy(InventoryItem $inventoryItem)
    {
        return parent::destroy($inventoryItem->id);
    }

    public function printSingle(InventoryItem $inventoryItem)
    {
        return parent::printSingle($inventoryItem->id);
    }
}
