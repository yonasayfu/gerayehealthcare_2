<?php

namespace App\Services;

use App\DTOs\CreateInventoryMaintenanceRecordDTO;
use App\Models\InventoryMaintenanceRecord;
use Illuminate\Http\Request;

class InventoryMaintenanceRecordService extends BaseService
{
    public function __construct(InventoryMaintenanceRecord $inventoryMaintenanceRecord)
    {
        parent::__construct($inventoryMaintenanceRecord);
    }

    protected function applySearch($query, $search)
    {
        return $query->where('performed_by', 'ilike', "%$search%")
                  ->orWhere('description', 'ilike', "%$search%")
                  ->orWhereHas('item', fn($q) => $q->where('name', 'ilike', "%$search%"));
    }

    public function getAll(Request $request, array $with = [])
    {
        $query = $this->model->with(array_merge(['item'], $with));

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

    
}
