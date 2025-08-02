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

    public function export(Request $request): StreamedResponse
    {
        $query = InventoryItem::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%$search%")
                  ->orWhere('serial_number', 'like', "%$search%");
        }

        $inventoryItems = $query->get();

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

        if ($request->has('sort')) {
            $sortField = $request->input('sort');
            $sortDirection = $request->input('direction', 'asc');
            $query->orderBy($sortField, $sortDirection);
        }

        $inventoryItems = $query->get();

        $data = $inventoryItems->map(function($item, $index) {
            return [
                'index' => $index + 1,
                'name' => $item->name,
                'item_category' => $item->item_category,
                'item_type' => $item->item_type,
                'serial_number' => $item->serial_number,
                'status' => $item->status,
                'purchase_date' => $item->purchase_date,
                'warranty_expiry' => $item->warranty_expiry,
            ];
        })->toArray();

        $columns = [
            ['key' => 'index', 'label' => '#'],
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'item_category', 'label' => 'Category'],
            ['key' => 'item_type', 'label' => 'Type'],
            ['key' => 'serial_number', 'label' => 'Serial Number'],
            ['key' => 'status', 'label' => 'Status'],
            ['key' => 'purchase_date', 'label' => 'Purchase Date'],
            ['key' => 'warranty_expiry', 'label' => 'Warranty Expiry'],
        ];

        $title = 'Inventory Items Report';
        $documentTitle = 'Inventory Items Report';

        $pdf = Pdf::loadView('print-layout', compact('title', 'data', 'columns', 'documentTitle'))
                    ->setPaper('a4', 'landscape');
        return $pdf->stream('inventory-items.pdf');
    }

    public function printSingle(InventoryItem $inventoryItem)
    {
        $data = [
            ['label' => 'Name', 'value' => $inventoryItem->name],
            ['label' => 'Category', 'value' => $inventoryItem->item_category ?? '-'],
            ['label' => 'Type', 'value' => $inventoryItem->item_type ?? '-'],
            ['label' => 'Serial Number', 'value' => $inventoryItem->serial_number ?? '-'],
            ['label' => 'Status', 'value' => $inventoryItem->status ?? '-'],
            ['label' => 'Description', 'value' => $inventoryItem->description ?? '-'],
            ['label' => 'Purchase Date', 'value' => $inventoryItem->purchase_date ? \Carbon\Carbon::parse($inventoryItem->purchase_date)->format('Y-m-d') : '-'],
            ['label' => 'Warranty Expiry', 'value' => $inventoryItem->warranty_expiry ? \Carbon\Carbon::parse($inventoryItem->warranty_expiry)->format('Y-m-d') : '-'],
            ['label' => 'Supplier', 'value' => $inventoryItem->supplier->name ?? '-'],
            ['label' => 'Assigned To Type', 'value' => $inventoryItem->assigned_to_type ?? '-'],
            ['label' => 'Assigned To ID', 'value' => $inventoryItem->assigned_to_id ?? '-'],
            ['label' => 'Last Maintenance Date', 'value' => $inventoryItem->last_maintenance_date ? \Carbon\Carbon::parse($inventoryItem->last_maintenance_date)->format('Y-m-d') : '-'],
            ['label' => 'Next Maintenance Due', 'value' => $inventoryItem->next_maintenance_due ? \Carbon\Carbon::parse($inventoryItem->next_maintenance_due)->format('Y-m-d') : '-'],
            ['label' => 'Maintenance Schedule', 'value' => $inventoryItem->maintenance_schedule ?? '-'],
            ['label' => 'Notes', 'value' => $inventoryItem->notes ?? '-'],
        ];

        $columns = [
            ['key' => 'label', 'label' => 'Field', 'printWidth' => '30%'],
            ['key' => 'value', 'label' => 'Value', 'printWidth' => '70%'],
        ];

        $title = 'Inventory Item Details';
        $documentTitle = 'Inventory Item Details Report';

        $pdf = Pdf::loadView('print-layout', compact('title', 'data', 'columns', 'documentTitle'))
                    ->setPaper('a4', 'portrait');
        return $pdf->stream("inventory_item_" . $inventoryItem->id . ".pdf");
    }

    public function generateSinglePdf(InventoryItem $inventoryItem)
    {
        $pdf = Pdf::loadView('pdf.inventory_items_single_pdf', ['inventoryItem' => $inventoryItem]);
        return $pdf->stream('inventory_item_' . $inventoryItem->id . '.pdf');
    }

    public function generatePdf(Request $request)
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

        $inventoryItems = $query->get();

        $data = $inventoryItems->map(function($item, $index) {
            return [
                'index' => $index + 1,
                'name' => $item->name,
                'item_category' => $item->item_category,
                'item_type' => $item->item_type,
                'serial_number' => $item->serial_number,
                'status' => $item->status,
                'purchase_date' => $item->purchase_date,
                'warranty_expiry' => $item->warranty_expiry,
            ];
        })->toArray();

        $columns = [
            ['key' => 'index', 'label' => '#'],
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'item_category', 'label' => 'Category'],
            ['key' => 'item_type', 'label' => 'Type'],
            ['key' => 'serial_number', 'label' => 'Serial Number'],
            ['key' => 'status', 'label' => 'Status'],
            ['key' => 'purchase_date', 'label' => 'Purchase Date'],
            ['key' => 'warranty_expiry', 'label' => 'Warranty Expiry'],
        ];

        $title = 'Inventory Items Report';
        $documentTitle = 'Inventory Items Report';

        $pdf = Pdf::loadView('print-layout', compact('title', 'data', 'columns', 'documentTitle'))
                    ->setPaper('a4', 'landscape');
        return $pdf->stream('inventory-items.pdf');
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
