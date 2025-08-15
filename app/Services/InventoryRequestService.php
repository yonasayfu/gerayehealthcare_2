<?php

namespace App\Services;

use App\Models\InventoryRequest;
use App\Models\InventoryItem;
use App\Models\InventoryAlert;
use Illuminate\Http\Request;

class InventoryRequestService extends BaseService
{
    public function __construct(InventoryRequest $inventoryRequest)
    {
        parent::__construct($inventoryRequest);
    }

    protected function applySearch($query, $search)
    {
        return $query->whereHas('item', fn($q) => $q->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('requester', fn($q) => $q->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%"))
                  ->orWhere('reason', 'like', "%{$search}%");
    }

    public function getAll(Request $request, array $with = [])
    {
        $query = $this->model->with(array_merge(['requester', 'approver', 'item'], $with));

        if ($request->has('search')) {
            $this->applySearch($query, $request->input('search'));
        }

        if ($request->filled('sort') && !empty($request->input('sort'))) {
            $sortField = $request->input('sort');
            $sortDirection = $request->input('direction', 'asc');

            // Handle sorting for relationships
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

    public function create(array|object $data): InventoryRequest
    {
        $data = is_object($data) ? (array) $data : $data;
        return parent::create($data);
    }

    public function update(int $id, array|object $data): InventoryRequest
    {
        $inventoryRequest = parent::update($id, $data);
        $this->checkInventoryAlert($inventoryRequest);
        return $inventoryRequest;
    }

    

    
}
