<?php

namespace App\Services;

use App\Http\Config\AdditionalExportConfigs;
use App\Http\Traits\ExportableTrait;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;

class InventoryTransactionService extends BaseService
{
    use ExportableTrait;

    public function __construct(InventoryTransaction $inventoryTransaction)
    {
        parent::__construct($inventoryTransaction);
    }

    public function getById(int $id, array $with = []): InventoryTransaction
    {
        $with = array_unique(array_merge(['item', 'performedBy', 'request', 'fromAssignedTo', 'toAssignedTo'], $with));

        return $this->model
            ->with($with)
            ->findOrFail($id);
    }

    protected function applySearch($query, $search)
    {
        return $query->where('transaction_type', 'like', "%$search%")
            ->orWhereHas('item', fn ($q) => $q->where('name', 'like', "%$search%"));
    }

    public function getAll(Request $request, array $with = [])
    {
        $query = $this->model->with(array_merge(['item', 'performedBy', 'request', 'fromAssignedTo', 'toAssignedTo'], $with));

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

    protected function applySorting($query, Request $request)
    {
        if ($request->has('sort')) {
            $direction = $request->input('direction', 'asc');
            $query->orderBy($request->input('sort'), $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, $this->model, AdditionalExportConfigs::getInventoryTransactionConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, $this->model, AdditionalExportConfigs::getInventoryTransactionConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, $this->model, AdditionalExportConfigs::getInventoryTransactionConfig());
    }

    public function printSingle(InventoryTransaction $inventoryTransaction, Request $request)
    {
        return $this->handlePrintSingle($request, $inventoryTransaction, AdditionalExportConfigs::getInventoryTransactionConfig());
    }
}
