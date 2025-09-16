<?php

namespace App\Services\InventoryMaintenanceRecord;

use App\Models\InventoryMaintenanceRecord;
use App\Services\Base\BaseService;
use Illuminate\Http\Request;

class InventoryMaintenanceRecordService extends BaseService
{
    public function __construct(InventoryMaintenanceRecord $inventoryMaintenanceRecord)
    {
        parent::__construct($inventoryMaintenanceRecord);
    }

    protected function applySearch($query, $search)
    {
        $query->where('description', 'like', "%{$search}%")
            ->orWhere('maintenance_date', 'like', "%{$search}%");
    }

    public function getAll(Request $request, array $with = [])
    {
        // Always eager load item for listings
        $with = array_unique(array_merge(['item'], $with));

        return parent::getAll($request, $with);
    }

    public function getById(int $id, array $with = [])
    {
        // Always eager load item for Show view and merge any additional relations
        $with = array_unique(array_merge(['item'], $with));

        return parent::getById($id, $with);
    }
}
