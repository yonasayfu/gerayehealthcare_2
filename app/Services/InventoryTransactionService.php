<?php

namespace App\Services;

use App\DTOs\CreateInventoryTransactionDTO;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;

class InventoryTransactionService extends BaseService
{
    public function __construct(InventoryTransaction $inventoryTransaction)
    {
        parent::__construct($inventoryTransaction);
    }

    public function getById(int $id): InventoryTransaction
    {
        return $this->model
            ->with(['item', 'performedBy', 'request', 'fromAssignedTo', 'toAssignedTo'])
            ->findOrFail($id);
    }

    protected function applySearch($query, $search)
    {
        return $query->where('transaction_type', 'like', "%$search%")
                  ->orWhereHas('item', fn($q) => $q->where('name', 'like', "%$search%"));
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
}
