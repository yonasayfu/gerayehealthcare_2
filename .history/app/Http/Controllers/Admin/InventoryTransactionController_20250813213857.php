<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\InventoryTransactionService;
use App\Models\InventoryTransaction;
use App\Services\Validation\Rules\InventoryTransactionRules;
use App\DTOs\CreateInventoryTransactionDTO;

class InventoryTransactionController extends BaseController
{
    public function __construct(InventoryTransactionService $inventoryTransactionService)
    {
        parent::__construct(
            $inventoryTransactionService,
            InventoryTransactionRules::class,
            'Admin/InventoryTransactions',
            'inventoryTransactions',
            InventoryTransaction::class,
            CreateInventoryTransactionDTO::class
        );
    }

    
}
