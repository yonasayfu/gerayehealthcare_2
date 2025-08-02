<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\AdditionalExportConfigs;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use League\Csv\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class InventoryItemController extends Controller
{
    use ExportableTrait;
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

        if ($request->has('sort')) {
            $sortField = $request->input('sort');
            $sortDirection = $request->input('direction', 'asc');
            $query->orderBy($sortField, $sortDirection);
        }

        $perPage = $request->input('per_page', 10);
        $inventoryItems = $query->paginate($perPage);

        return Inertia::render('Admin/InventoryItems/Index', [
            'inventoryItems' => $inventoryItems,
            'filters' => $request->all('search', 'sort', 'direction', 'per_page'),
        ]);
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, InventoryItem::class, AdditionalExportConfigs::getInventoryItemConfig());
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
        return $this->handlePrintAll($request, InventoryItem::class, AdditionalExportConfigs::getInventoryItemConfig());
    }

    public function printSingle(InventoryItem $inventoryItem)
    {
        return $this->handlePrintSingle($inventoryItem, AdditionalExportConfigs::getInventoryItemConfig());
    }

    public function generateSinglePdf(InventoryItem $inventoryItem)
    {
        return $this->handlePrintSingle($inventoryItem, AdditionalExportConfigs::getInventoryItemConfig());
    }

    public function generatePdf(Request $request)
    {
        return $this->handlePrintCurrent($request, InventoryItem::class, AdditionalExportConfigs::getInventoryItemConfig());
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
