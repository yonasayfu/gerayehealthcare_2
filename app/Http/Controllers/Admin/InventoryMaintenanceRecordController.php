<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryMaintenanceRecord;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InventoryMaintenanceRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = InventoryMaintenanceRecord::with('item');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('performed_by', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%")
                  ->orWhereHas('item', fn($q) => $q->where('name', 'like', "%$search%"));
        }

        $maintenanceRecords = $query->paginate(10);

        return Inertia::render('Admin/InventoryMaintenanceRecords/Index', [
            'maintenanceRecords' => $maintenanceRecords,
            'filters' => $request->all('search'),
        ]);
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
}
