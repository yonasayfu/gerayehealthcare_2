<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreateInventoryTransactionDTO;
use App\Http\Controllers\Base\BaseController;
use App\Models\InventoryTransaction;
use App\Services\InventoryTransactionService;
use App\Services\Validation\Rules\InventoryTransactionRules;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\InventoryItem;
use App\Models\Staff;

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

    public function printAll(Request $request)
    {
        return app(InventoryTransactionService::class)->printAll($request);
    }

    public function printSingle(InventoryTransaction $inventoryTransaction, Request $request)
    {
        return app(InventoryTransactionService::class)->printSingle($inventoryTransaction, $request);
    }

    /**
     * Provide populated selects for Edit view to normalize dropdown data.
     */
    public function edit($id)
    {
        $transaction = $this->service->getById($id);
        $staff = Staff::select('id', 'first_name', 'last_name')->orderBy('first_name')->get();
        $inventoryItems = InventoryItem::select('id', 'name')->orderBy('name')->get();

        return Inertia::render($this->viewName.'/Edit', [
            lcfirst(class_basename($this->modelClass)) => $transaction,
            'staffList' => $staff,
            'inventoryItems' => $inventoryItems,
        ]);
    }

    /**
     * Provide populated selects for Create view to normalize dropdown data.
     */
    public function create()
    {
        $staff = Staff::select('id', 'first_name', 'last_name')->orderBy('first_name')->get();
        $inventoryItems = InventoryItem::select('id', 'name')->orderBy('name')->get();

        return Inertia::render($this->viewName.'/Create', [
            'staffList' => $staff,
            'inventoryItems' => $inventoryItems,
        ]);
    }
}
