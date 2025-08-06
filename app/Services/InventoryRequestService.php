<?php

namespace App\Services;

use App\Models\InventoryRequest;
use App\Models\InventoryItem;
use App\Models\InventoryAlert;
use Illuminate\Http\Request;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\AdditionalExportConfigs;

class InventoryRequestService extends BaseService
{
    use ExportableTrait;

    public function __construct(InventoryRequest $inventoryRequest)
    {
        parent::__construct($inventoryRequest);
    }

    protected function applySearch($query, $search)
    {
        return $query->whereHas('item', fn($q) => $q->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('requester', fn($q) => $q->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%"));
    }

    public function getAll(Request $request)
    {
        $query = $this->model->with(['requester', 'approver', 'item']);

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

    

    public function export(Request $request)
    {
        return $this->handleExport($request, InventoryRequest::class, AdditionalExportConfigs::getInventoryRequestConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, InventoryRequest::class, AdditionalExportConfigs::getInventoryRequestConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, InventoryRequest::class, AdditionalExportConfigs::getInventoryRequestConfig());
    }

    public function printSingle($id)
    {
        $inventoryRequest = $this->getById($id);
        return $this->handlePrintSingle($inventoryRequest, AdditionalExportConfigs::getInventoryRequestConfig());
    }
}
