<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryAlert;
use Illuminate\Http\Request;
use Inertia\Inertia;
use League\Csv\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class InventoryAlertController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $query = InventoryAlert::with('item');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('alert_type', 'like', "%$search%")
                  ->orWhere('message', 'like', "%$search%")
                  ->orWhereHas('item', fn($q) => $q->where('name', 'like', "%$search%"));
        }

        $inventoryAlerts = $query->paginate($perPage);

        return Inertia::render('Admin/InventoryAlerts/Index', [
            'inventoryAlerts' => $inventoryAlerts,
            'filters' => [
                'search' => $request->input('search'),
                'per_page' => $request->input('per_page', 10),
            ],
        ]);
    }

    public function export(): StreamedResponse
    {
        $inventoryAlerts = InventoryAlert::with('item')->get();

        $csv = Writer::createFromString('');
        $csv->insertOne(['ID', 'Item', 'Alert Type', 'Threshold Value', 'Message', 'Is Active', 'Triggered At']);

        foreach ($inventoryAlerts as $alert) {
            $csv->insertOne([
                $alert->id,
                $alert->item->name ?? 'N/A',
                $alert->alert_type,
                $alert->threshold_value,
                $alert->message,
                $alert->is_active ? 'Yes' : 'No',
                $alert->triggered_at,
            ]);
        }

        return response()->streamDownload(function () use ($csv) {
            echo $csv->toString();
        }, 'inventory_alerts.csv', [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="inventory_alerts.csv"',
        ]);
    }

    public function printAll(Request $request)
    {
        $query = InventoryAlert::with('item');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('alert_type', 'like', "%$search%")
                  ->orWhere('message', 'like', "%$search%")
                  ->orWhereHas('item', fn($q) => $q->where('name', 'like', "%$search%"));
        }

        $inventoryAlerts = $query->get();

        return Inertia::render('Admin/InventoryAlerts/PrintAll', [
            'inventoryAlerts' => $inventoryAlerts,
        ]);
    }

    public function printSingle(InventoryAlert $inventoryAlert)
    {
        return Inertia::render('Admin/InventoryAlerts/PrintSingle', [
            'inventoryAlert' => $inventoryAlert->load('item'),
        ]);
    }

    public function generatePdf(Request $request)
    {
        $query = InventoryAlert::with('item');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('alert_type', 'like', "%$search%")
                  ->orWhere('message', 'like', "%$search%")
                  ->orWhereHas('item', fn($q) => $q->where('name', 'like', "%$search%"));
        }

        $inventoryAlerts = $query->get();

        $pdf = Pdf::loadView('pdf.inventory-alerts', ['inventoryAlerts' => $inventoryAlerts])->setPaper('a4', 'landscape');
        return $pdf->stream('inventory_alerts.pdf');
    }

    public function create()
    {
        return Inertia::render('Admin/InventoryAlerts/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'nullable|exists:inventory_items,id',
            'alert_type' => 'required|string|max:255',
            'threshold_value' => 'nullable|string|max:255',
            'message' => 'required|string',
            'is_active' => 'boolean',
        ]);

        InventoryAlert::create($validated);

        return redirect()->route('admin.inventory-alerts.index')->with('success', 'Inventory alert created successfully.');
    }

    public function edit(InventoryAlert $inventoryAlert)
    {
        return Inertia::render('Admin/InventoryAlerts/Edit', [
            'inventoryAlert' => $inventoryAlert->load('item'),
        ]);
    }

    public function update(Request $request, InventoryAlert $inventoryAlert)
    {
        $validated = $request->validate([
            'item_id' => 'nullable|exists:inventory_items,id',
            'alert_type' => 'required|string|max:255',
            'threshold_value' => 'nullable|string|max:255',
            'message' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $inventoryAlert->update($validated);

        return redirect()->route('admin.inventory-alerts.index')->with('success', 'Inventory alert updated successfully.');
    }

    public function destroy(InventoryAlert $inventoryAlert)
    {
        $inventoryAlert->delete();

        return redirect()->route('admin.inventory-alerts.index')->with('success', 'Inventory alert deleted successfully.');
    }

    public function count()
    {
        $count = InventoryAlert::where('is_active', true)->count();
        return response()->json(['count' => $count]);
    }
}
