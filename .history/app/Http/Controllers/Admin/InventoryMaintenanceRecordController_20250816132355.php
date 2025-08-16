<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\InventoryMaintenanceRecordService;
use App\Models\InventoryMaintenanceRecord;
use App\Services\Validation\Rules\InventoryMaintenanceRecordRules;
use App\DTOs\CreateInventoryMaintenanceRecordDTO;
use Illuminate\Http\Request;
use App\Services\InventoryItemService; // Import InventoryItemService
use App\Services\StaffService; // Import StaffService

use App\Http\Traits\ExportableTrait;
use Inertia\Inertia;

class InventoryMaintenanceRecordController extends BaseController
{
    use ExportableTrait;

    protected $inventoryItemService;
    protected $staffService; // Declare property

    public function __construct(InventoryMaintenanceRecordService $inventoryMaintenanceRecordService, InventoryItemService $inventoryItemService, StaffService $staffService)
    {
        parent::__construct(
            $inventoryMaintenanceRecordService,
            InventoryMaintenanceRecordRules::class,
            'Admin/InventoryMaintenanceRecords',
            'maintenanceRecords',
            InventoryMaintenanceRecord::class,
            CreateInventoryMaintenanceRecordDTO::class
        );
        $this->inventoryItemService = $inventoryItemService;
        $this->staffService = $staffService; // Assign service
    }

    public function show($id)
    {
        $record = $this->service->getById($id, ['item', 'performedByStaff']); // Eager load 'item' and 'performedByStaff'
        return Inertia::render('Admin/InventoryMaintenanceRecords/Show', [
            'maintenanceRecord' => $record,
        ]);
    }

    public function edit($id)
    {
        $record = $this->service->getById($id, ['item', 'performedByStaff']); // Eager load 'item' and 'performedByStaff'
        $inventoryItems = $this->inventoryItemService->getAll(new Request())->toArray();
        $staffMembers = $this->staffService->getAll(new Request())->toArray(); // Fetch all staff
        return Inertia::render('Admin/InventoryMaintenanceRecords/Edit', [
            'maintenanceRecord' => $record,
            'inventoryItems' => $inventoryItems['data'],
            'staffMembers' => $staffMembers['data'], // Pass staff members
        ]);
    }

    public function create()
    {
        $inventoryItems = $this->inventoryItemService->getAll(new Request())->toArray();
        $staffMembers = $this->staffService->getAll(new Request())->toArray(); // Fetch all staff
        return Inertia::render('Admin/InventoryMaintenanceRecords/Create', [
            'inventoryItems' => $inventoryItems['data'],
            'staffMembers' => $staffMembers['data'], // Pass staff members
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
