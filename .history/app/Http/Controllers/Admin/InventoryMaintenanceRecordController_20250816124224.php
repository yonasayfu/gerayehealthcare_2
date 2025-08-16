<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\InventoryMaintenanceRecordService;
use App\Models\InventoryMaintenanceRecord;
use App\Services\Validation\Rules\InventoryMaintenanceRecordRules;
use App\DTOs\CreateInventoryMaintenanceRecordDTO;
use Illuminate\Http\Request;
use App\Services\InventoryItemService; // Import InventoryItemService

use App\Http\Traits\ExportableTrait;
use Inertia\Inertia;

class InventoryMaintenanceRecordController extends BaseController
{
    use ExportableTrait;

    protected $inventoryItemService; // Declare property

    public function __construct(InventoryMaintenanceRecordService $inventoryMaintenanceRecordService, InventoryItemService $inventoryItemService)
    {
        parent::__construct(
            $inventoryMaintenanceRecordService,
            InventoryMaintenanceRecordRules::class,
            'Admin/InventoryMaintenanceRecords',
            'maintenanceRecords',
            InventoryMaintenanceRecord::class,
            CreateInventoryMaintenanceRecordDTO::class
        );
        $this->inventoryItemService = $inventoryItemService; // Assign service
    }

    public function show($id)
    {
        $record = $this->service->getById($id, ['item']); // Eager load 'item'
        return Inertia::render('Admin/InventoryMaintenanceRecords/Show', [
            'maintenanceRecord' => $record,
        ]);
    }

    public function edit($id)
    {
        $record = $this->service->getById($id, ['item']); // Eager load 'item'
        $inventoryItems = app(\App\Services\InventoryItemService::class)->getAll(new Request())->toArray(); // Fetch all items
        return Inertia::render('Admin/InventoryMaintenanceRecords/Edit', [
            'maintenanceRecord' => $record,
            'inventoryItems' => $inventoryItems['data'], // Pass only the data array
        ]);
    }

    public function create()
    {
        $inventoryItems = app(\App\Services\InventoryItemService::class)->getAll(new Request())->toArray(); // Fetch all items
        return Inertia::render('Admin/InventoryMaintenanceRecords/Create', [
            'inventoryItems' => $inventoryItems['data'], // Pass only the data array
        ]);
    }

    public function export(Request $request)
    {
        return $this->service->export($request);
    }

    public function printAll(Request $request)
    {
        return $this->service->printAll($request);
    }

    public function printCurrent(Request $request)
    {
        return $this->service->printCurrent($request);
    }

    public function printSingle(InventoryMaintenanceRecord $inventoryMaintenanceRecord, Request $request)
    {
        return $this->service->printSingle($inventoryMaintenanceRecord, $request);
    }
}
