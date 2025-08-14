<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\InventoryTransactionService;
use App\Models\InventoryTransaction;
use App\Services\Validation\Rules\InventoryTransactionRules;
use App\DTOs\CreateInventoryTransactionDTO;
use Illuminate\Http\Request;
use Inertia\Inertia;

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

    public function printAll()
    {
        return app(InventoryTransactionService::class)->printAll(request());
    }

    public function printCurrent()
    {
        return app(InventoryTransactionService::class)->printCurrent(request());
    }

    public function printSingle(InventoryTransaction $inventory_transaction)
    {
        return app(InventoryTransactionService::class)->printSingle($inventory_transaction, request());
    }
}
