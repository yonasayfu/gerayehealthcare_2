<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryAlert;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InventoryAlertController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = InventoryAlert::with('item');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('alert_type', 'like', "%$search%")
                  ->orWhere('message', 'like', "%$search%")
                  ->orWhereHas('item', fn($q) => $q->where('name', 'like', "%$search%"));
        }

        $inventoryAlerts = $query->paginate(10);

        return Inertia::render('Admin/InventoryAlerts/Index', [
            'inventoryAlerts' => $inventoryAlerts,
            'filters' => $request->all('search'),
        ]);
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
}
