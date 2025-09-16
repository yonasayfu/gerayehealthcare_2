<?php

namespace App\Services\InventoryRequest;

use App\Models\InventoryRequest;
use App\Services\Base\BaseService;
use Illuminate\Http\Request;

class InventoryRequestService extends BaseService
{
    public function __construct(InventoryRequest $inventoryRequest)
    {
        parent::__construct($inventoryRequest);
    }

    public function getById(int $id, array $with = [])
    {
        $with = array_unique(array_merge(['requester', 'item', 'approver'], $with));

        return $this->model->with($with)->findOrFail($id);
    }

    protected function applySearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->whereHas('item', function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%");
            })
                ->orWhereHas('requester', function ($q) use ($search) {
                    $q->where('first_name', 'ilike', "%{$search}%")
                        ->orWhere('last_name', 'ilike', "%{$search}%");
                })
                ->orWhere('reason', 'ilike', "%{$search}%");
        });
    }

    public function getAll(Request $request, array $with = [])
    {
        $query = $this->model->with(array_merge(['requester', 'approver', 'item'], $with));

        if ($request->has('search')) {
            $this->applySearch($query, $request->input('search'));
        }

        if ($request->filled('sort') && ! empty($request->input('sort'))) {
            $sortField = $request->input('sort');
            $sortDirection = $request->input('direction', 'asc');

            if ($sortField === 'requester_id') {
                $query->join('staff', 'inventory_requests.requester_id', '=', 'staff.id')
                    ->orderBy('staff.first_name', $sortDirection)
                    ->select('inventory_requests.*');
            } elseif ($sortField === 'item_id') {
                $query->join('inventory_items', 'inventory_requests.item_id', '=', 'inventory_items.id')
                    ->orderBy('inventory_items.name', $sortDirection)
                    ->select('inventory_requests.*');
            } else {
                $query->orderBy($sortField, $sortDirection);
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($request->input('per_page', 10));
    }
}
