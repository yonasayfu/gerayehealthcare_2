<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\InventoryItemService;
use App\Models\InventoryItem;
use App\Services\Validation\Rules\InventoryItemRules;
use App\Http\Controllers\Base\BaseController;

class InventoryItemController extends BaseController
{
    public function __construct(InventoryItemService $inventoryItemService)
    {
        parent::__construct(
            $inventoryItemService,
            InventoryItemRules::class,
            'Admin/InventoryItems',
            'inventoryItems',
            InventoryItem::class,
            CreateInventoryItemDTO::class
        );
    }

    
}
