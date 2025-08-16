<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreateInventoryTransactionDTO;
use App\Http\Controllers\Base\BaseController;
use App\Models\InventoryTransaction;
use App\Services\InventoryTransactionService;
use App\Services\Validation\Rules\InventoryTransactionRules;
use Illuminate\Http\Request;

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

    public function export(Request $request)
    {
        return app(InventoryTransactionService::class)->export($request);
    }

    public function printCurrent()
    {
        return app(InventoryTransactionService::class)->printCurrent(request());
    }

}
