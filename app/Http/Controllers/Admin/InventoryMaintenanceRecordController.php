<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\AdditionalExportConfigs;
use App\Models\InventoryMaintenanceRecord;
use Illuminate\Http\Request;
use Inertia\Inertia;
use League\Csv\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class InventoryMaintenanceRecordController extends Controller
{
    use ExportableTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = InventoryMaintenanceRecord::with('item');

        $search = $request->input('search');
        $sort = $request->input('sort', '');
        $direction = $request->input('direction', 'asc');
        $perPage = $request->input('per_page', 10);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('performed_by', 'ilike', "%$search%")
                  ->orWhere('description', 'ilike', "%$search%")
                  ->orWhereHas('item', fn($q) => $q->where('name', 'ilike', "%$search%"));
        }

        $maintenanceRecords = $query->paginate($perPage);

        return Inertia::render('Admin/InventoryMaintenanceRecords/Index', [
            'maintenanceRecords' => $maintenanceRecords,
            'filters' => [
                'search' => $search,
                'sort' => $sort,
                'direction' => $direction,
                'per_page' => $perPage,
            ],
        ]);
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, InventoryMaintenanceRecord::class, AdditionalExportConfigs::getInventoryMaintenanceRecordConfig());
    }

    public function create()
    {
        return Inertia::render('Admin/InventoryMaintenanceRecords/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:inventory_items,id',
            'scheduled_date' => 'nullable|date',
            'actual_date' => 'nullable|date|after_or_equal:scheduled_date',
            'performed_by' => 'nullable|string|max:255',
            'cost' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'next_due_date' => 'nullable|date|after_or_equal:actual_date',
            'status' => 'required|string|in:Scheduled,Completed,Overdue,Cancelled',
        ]);

        InventoryMaintenanceRecord::create($validated);

        return redirect()->route('admin.inventory-maintenance-records.index')->with('success', 'Maintenance record created successfully.');
    }

    public function edit(InventoryMaintenanceRecord $inventoryMaintenanceRecord)
    {
        return Inertia::render('Admin/InventoryMaintenanceRecords/Edit', [
            'maintenanceRecord' => $inventoryMaintenanceRecord->load('item'),
        ]);
    }

    public function update(Request $request, InventoryMaintenanceRecord $inventoryMaintenanceRecord)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:inventory_items,id',
            'scheduled_date' => 'nullable|date',
            'actual_date' => 'nullable|date|after_or_equal:scheduled_date',
            'performed_by' => 'nullable|string|max:255',
            'cost' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'next_due_date' => 'nullable|date|after_or_equal:actual_date',
            'status' => 'required|string|in:Scheduled,Completed,Overdue,Cancelled',
        ]);

        $inventoryMaintenanceRecord->update($validated);

        return redirect()->route('admin.inventory-maintenance-records.index')->with('success', 'Maintenance record updated successfully.');
    }

    public function destroy(InventoryMaintenanceRecord $inventoryMaintenanceRecord)
    {
        $inventoryMaintenanceRecord->delete();

        return redirect()->route('admin.inventory-maintenance-records.index')->with('success', 'Maintenance record deleted successfully.');
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, InventoryMaintenanceRecord::class, AdditionalExportConfigs::getInventoryMaintenanceRecordConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, InventoryMaintenanceRecord::class, AdditionalExportConfigs::getInventoryMaintenanceRecordConfig());
    }

    public function printSingle(InventoryMaintenanceRecord $inventoryMaintenanceRecord)
    {
        return $this->handlePrintSingle($inventoryMaintenanceRecord, AdditionalExportConfigs::getInventoryMaintenanceRecordConfig());
    }

    public function generatePdf(Request $request)
    {
        return $this->handlePrintCurrent($request, InventoryMaintenanceRecord::class, AdditionalExportConfigs::getInventoryMaintenanceRecordConfig());
    }
}
