<?php

namespace App\Services;

use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\AdditionalExportConfigs;

class InventoryTransactionService extends BaseService
{
    use ExportableTrait;

    public function __construct(InventoryTransaction $inventoryTransaction)
    {
        parent::__construct($inventoryTransaction);
    }

    protected function applySearch($query, $search)
    {
        return $query->where('transaction_type', 'like', "%$search%")
                  ->orWhereHas('item', fn($q) => $q->where('name', 'like', "%$search%"));
    }

    public function getAll(Request $request)
    {
        $query = $this->model->with(['item', 'performedBy']);

        if ($request->has('search')) {
            $this->applySearch($query, $request->input('search'));
        }

        if ($request->has('sort')) {
            $direction = $request->input('direction', 'asc');
            $query->orderBy($request->input('sort'), $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($request->input('per_page', 10));
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, InventoryTransaction::class, AdditionalExportConfigs::getInventoryTransactionConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, InventoryTransaction::class, AdditionalExportConfigs::getInventoryTransactionConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, InventoryTransaction::class, AdditionalExportConfigs::getInventoryTransactionConfig());
    }

    public function printSingle($id)
    {
        $inventoryTransaction = $this->getById($id);
        return $this->handlePrintSingle($inventoryTransaction, AdditionalExportConfigs::getInventoryTransactionConfig());
    }
}
