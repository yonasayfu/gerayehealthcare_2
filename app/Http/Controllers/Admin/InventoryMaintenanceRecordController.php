<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\InventoryMaintenanceRecordService;
use App\Models\InventoryMaintenanceRecord;
use App\Services\Validation\Rules\InventoryMaintenanceRecordRules;
use Illuminate\Http\Request;

class InventoryMaintenanceRecordController extends BaseController
{
    public function __construct(InventoryMaintenanceRecordService $inventoryMaintenanceRecordService)
    {
        parent::__construct(
            $inventoryMaintenanceRecordService,
            InventoryMaintenanceRecordRules::class,
            'Admin/InventoryMaintenanceRecords',
            'maintenanceRecords',
            InventoryMaintenanceRecord::class
        );
    }

    public function show(InventoryMaintenanceRecord $inventoryMaintenanceRecord)
    {
        return parent::show($inventoryMaintenanceRecord->id);
    }

    public function edit(InventoryMaintenanceRecord $inventoryMaintenanceRecord)
    {
        return parent::edit($inventoryMaintenanceRecord->id);
    }

    public function update(Request $request, InventoryMaintenanceRecord $inventoryMaintenanceRecord)
    {
        return parent::update($request, $inventoryMaintenanceRecord->id);
    }

    public function destroy(InventoryMaintenanceRecord $inventoryMaintenanceRecord)
    {
        return parent::destroy($inventoryMaintenanceRecord->id);
    }

    public function printSingle(InventoryMaintenanceRecord $inventoryMaintenanceRecord)
    {
        return parent::printSingle($inventoryMaintenanceRecord->id);
    }
}