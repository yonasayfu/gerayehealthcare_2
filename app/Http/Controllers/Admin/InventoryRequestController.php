<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InventoryRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = InventoryRequest::with(['requester', 'approver', 'item']);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereHas('item', fn($q) => $q->where('name', 'like', "%$search%"))
                  ->orWhereHas('requester', fn($q) => $q->where('first_name', 'like', "%$search%")->orWhere('last_name', 'like', "%$search%"));
        }

        $inventoryRequests = $query->paginate(10);

        return Inertia::render('Admin/InventoryRequests/Index', [
            'inventoryRequests' => $inventoryRequests,
            'filters' => $request->all('search'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/InventoryRequests/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'requester_id' => 'required|exists:staff,id',
            'item_id' => 'required|exists:inventory_items,id',
            'quantity_requested' => 'required|integer|min:1',
            'reason' => 'nullable|string',
            'priority' => 'required|string|in:Low,Normal,High,Urgent',
            'needed_by_date' => 'nullable|date',
        ]);

        InventoryRequest::create($validated);

        return redirect()->route('admin.inventory-requests.index')->with('success', 'Inventory request created successfully.');
    }

    public function edit(InventoryRequest $inventoryRequest)
    {
        return Inertia::render('Admin/InventoryRequests/Edit', [
            'inventoryRequest' => $inventoryRequest->load(['requester', 'approver', 'item']),
        ]);
    }

    public function update(Request $request, InventoryRequest $inventoryRequest)
    {
        $validated = $request->validate([
            'requester_id' => 'required|exists:staff,id',
            'approver_id' => 'nullable|exists:staff,id',
            'item_id' => 'required|exists:inventory_items,id',
            'quantity_requested' => 'required|integer|min:1',
            'quantity_approved' => 'nullable|integer|min:0|max:quantity_requested',
            'reason' => 'nullable|string',
            'status' => 'required|string|in:Pending,Approved,Rejected,Fulfilled,Partially Fulfilled',
            'priority' => 'required|string|in:Low,Normal,High,Urgent',
            'needed_by_date' => 'nullable|date',
            'approved_at' => 'nullable|date',
            'fulfilled_at' => 'nullable|date',
        ]);

        $inventoryRequest->update($validated);

        return redirect()->route('admin.inventory-requests.index')->with('success', 'Inventory request updated successfully.');
    }

    public function destroy(InventoryRequest $inventoryRequest)
    {
        $inventoryRequest->delete();

        return redirect()->route('admin.inventory-requests.index')->with('success', 'Inventory request deleted successfully.');
    }
}
