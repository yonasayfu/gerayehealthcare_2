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
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(InventoryMaintenanceRecord $inventoryMaintenanceRecord)
    {
        return Inertia::render('Admin/InventoryMaintenanceRecords/Show', [
            'inventoryMaintenanceRecord' => $inventoryMaintenanceRecord,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InventoryMaintenanceRecord $inventoryMaintenanceRecord)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InventoryMaintenanceRecord $inventoryMaintenanceRecord)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InventoryMaintenanceRecord $inventoryMaintenanceRecord)
    {
        //
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
        $query = InventoryMaintenanceRecord::with('item');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('performed_by', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%")
                  ->orWhereHas('item', fn($q) => $q->where('name', 'like', "%$search%"));
        }

        $maintenanceRecords = $query->get();

        return Inertia::render('Admin/InventoryMaintenanceRecords/PrintAll', [
            'maintenanceRecords' => $maintenanceRecords,
        ]);
    }

    public function printSingle(InventoryMaintenanceRecord $inventoryMaintenanceRecord)
    {
        return Inertia::render('Admin/InventoryMaintenanceRecords/PrintSingle', [
            'maintenanceRecord' => $inventoryMaintenanceRecord->load('item'),
        ]);
    }

    public function generatePdf(Request $request)
    {
        $query = InventoryMaintenanceRecord::with('item');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('performed_by', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%")
                  ->orWhereHas('item', fn($q) => $q->where('name', 'like', "%$search%"));
        }

        $maintenanceRecords = $query->get();

        $pdf = Pdf::loadView('pdf.inventory-maintenance-records', ['maintenanceRecords' => $maintenanceRecords])->setPaper('a4', 'landscape');
        return $pdf->stream('inventory_maintenance_records.pdf');
    }
}
