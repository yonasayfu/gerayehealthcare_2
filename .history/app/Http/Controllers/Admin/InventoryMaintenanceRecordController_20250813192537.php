<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\InventoryMaintenanceRecordService;
use App\Models\InventoryMaintenanceRecord;
use App\Services\Validation\Rules\InventoryMaintenanceRecordRules;
use Illuminate\Http\Request;
use App\Http\Controllers\Base\BaseController;

class InventoryMaintenanceRecordController extends BaseController
{
    public function __construct(InventoryMaintenanceRecordService $inventoryMaintenanceRecordService)
    {
        parent::__construct(
            $inventoryMaintenanceRecordService,
            InventoryMaintenanceRecordRules::class,
            'Admin/InventoryMaintenanceRecords',
            'maintenanceRecords',
            InventoryMaintenanceRecord::class,
            CreateInventoryMaintenanceRecordDTO::class
        );
    }

   
}