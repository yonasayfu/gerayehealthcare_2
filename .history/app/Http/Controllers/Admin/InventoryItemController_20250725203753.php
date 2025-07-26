<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use League\Csv\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class InventoryItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = InventoryItem::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%$search%")
                  ->orWhere('serial_number', 'like', "%$search%");
        }

        $inventoryItems = $query->paginate(10);

        return Inertia::render('Admin/InventoryItems/Index', [
            'inventoryItems' => $inventoryItems,
            'filters' => $request->all('search'),
        ]);
    }

    public function export(): StreamedResponse
    {
        $inventoryItems = InventoryItem::all();

        $csv = Writer::createFromString('');
        $csv->insertOne(['ID', 'Name', 'Description', 'Category', 'Type', 'Serial Number', 'Purchase Date', 'Warranty Expiry', 'Supplier ID', 'Assigned To Type', 'Assigned To ID', 'Last Maintenance Date', 'Next Maintenance Due', 'Maintenance Schedule', 'Notes', 'Status']);

        foreach ($inventoryItems as $item) {
            $csv->insertOne($item->toArray());
        }

        return response()->streamDownload(function () use ($csv) {
            echo $csv->toString();
        }, 'inventory_items.csv', [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="inventory_items.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/InventoryItems/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'item_category' => 'nullable|string|max:255',
            'item_type' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255|unique:inventory_items',
            'purchase_date' => 'nullable|date',
            'warranty_expiry' => 'nullable|date|after_or_equal:purchase_date',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'assigned_to_type' => 'nullable|string|max:50',
            'assigned_to_id' => 'nullable|integer',
            'last_maintenance_date' => 'nullable|date',
            'next_maintenance_due' => 'nullable|date|after_or_equal:last_maintenance_date',
            'maintenance_schedule' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'required|string|max:255',
        ]);

        InventoryItem::create($validated);

        return redirect()->route('admin.inventory-items.index')->with('success', 'Inventory item created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(InventoryItem $inventoryItem)
    {
        return Inertia::render('Admin/InventoryItems/Show', [
            'inventoryItem' => $inventoryItem,
        ]);
    }

    public function printAll(Request $request)
    {
        $query = InventoryItem::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%$search%")
                  ->orWhere('serial_number', 'like', "%$search%");
        }

        $inventoryItems = $query->get();

        return Inertia::render('Admin/InventoryItems/PrintAll', [
            'inventoryItems' => $inventoryItems,
        ]);
    }

    public function printSingle(InventoryItem $inventoryItem)
    {
        return Inertia::render('Admin/InventoryItems/PrintSingle', [
            'inventoryItem' => $inventoryItem,
        ]);
    }

    public function generatePdf(Request $request)
    {
        $query = InventoryItem::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%$search%")
                  ->orWhere('serial_number', 'like', "%$search%");
        }

        $inventoryItems = $query->get();

        $pdf = Pdf::loadView('pdf.inventory-items', ['inventoryItems' => $inventoryItems])->setPaper('a4', 'landscape');
        return $pdf->stream('inventory_items.pdf');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InventoryItem $inventoryItem)
    {
        return Inertia::render('Admin/InventoryItems/Edit', [
            'inventoryItem' => $inventoryItem,
        ]);
    }

    public function update(Request $request, InventoryItem $inventoryItem)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'item_category' => 'nullable|string|max:255',
            'item_type' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255|unique:inventory_items,serial_number,' . $inventoryItem->id,
            'purchase_date' => 'nullable|date',
            'warranty_expiry' => 'nullable|date|after_or_equal:purchase_date',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'assigned_to_type' => 'nullable|string|max:50',
            'assigned_to_id' => 'nullable|integer',
            'last_maintenance_date' => 'nullable|date',
            'next_maintenance_due' => 'nullable|date|after_or_equal:last_maintenance_date',
            'maintenance_schedule' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'required|string|max:255',
        ]);

        $inventoryItem->update($validated);

        return redirect()->route('admin.inventory-items.index')->with('success', 'Inventory item updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InventoryItem $inventoryItem)
    {
        $inventoryItem->delete();

        return redirect()->route('admin.inventory-items.index')->with('success', 'Inventory item deleted successfully.');
    }
}
