<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use League\Csv\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;

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

        if ($request->has('sort_by') && $request->has('sort_direction')) {
            $sortBy = $request->input('sort_by');
            $sortDirection = $request->input('sort_direction');

            // Handle sorting for relationships
            if ($sortBy === 'requester_id') {
                $query->join('staff', 'inventory_requests.requester_id', '=', 'staff.id')
                      ->orderBy('staff.first_name', $sortDirection)
                      ->select('inventory_requests.*'); // Select inventory_requests columns to avoid ambiguity
            } elseif ($sortBy === 'item_id') {
                $query->join('inventory_items', 'inventory_requests.item_id', '=', 'inventory_items.id')
                      ->orderBy('inventory_items.name', $sortDirection)
                      ->select('inventory_requests.*');
            } else {
                $query->orderBy($sortBy, $sortDirection);
            }
        }

        $perPage = $request->input('per_page', 10);
        $inventoryRequests = $query->paginate($perPage);

        return Inertia::render('Admin/InventoryRequests/Index', [
            'inventoryRequests' => $inventoryRequests,
            'filters' => $request->all('search'),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $query = InventoryRequest::with(['requester', 'approver', 'item']);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereHas('item', fn($q) => $q->where('name', 'like', "%$search%"))
                  ->orWhereHas('requester', fn($q) => $q->where('first_name', 'like', "%$search%")->orWhere('last_name', 'like', "%$search%"));
        }

        if ($request->has('sort_by') && $request->has('sort_direction')) {
            $sortBy = $request->input('sort_by');
            $sortDirection = $request->input('sort_direction');

            if ($sortBy === 'requester_id') {
                $query->join('staff', 'inventory_requests.requester_id', '=', 'staff.id')
                      ->orderBy('staff.first_name', $sortDirection)
                      ->select('inventory_requests.*');
            } elseif ($sortBy === 'item_id') {
                $query->join('inventory_items', 'inventory_requests.item_id', '=', 'inventory_items.id')
                      ->orderBy('inventory_items.name', $sortDirection)
                      ->select('inventory_requests.*');
            } else {
                $query->orderBy($sortBy, $sortDirection);
            }
        }

        $inventoryRequests = $query->get();

        $csvContent = view('exports.inventory_requests', compact('inventoryRequests'))->render();

        return response()->streamDownload(function () use ($csvContent) {
            echo $csvContent;
        }, 'inventory_requests.csv', [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="inventory_requests.csv"',
        ]);
    }

    public function printAll(Request $request)
    {
        $query = InventoryRequest::with(['requester', 'approver', 'item']);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereHas('item', fn($q) => $q->where('name', 'like', "%$search%"))
                  ->orWhereHas('requester', fn($q) => $q->where('first_name', 'like', "%$search%")->orWhere('last_name', 'like', "%$search%"));
        }

        $inventoryRequests = $query->get();

        $pdf = Pdf::loadView('pdf.inventory_requests_pdf', ['inventoryRequests' => $inventoryRequests])->setPaper('a4', 'landscape');
        return $pdf->stream('inventory_requests_all.pdf');
    }

    public function printSingle(InventoryRequest $inventoryRequest)
    {
        $pdf = Pdf::loadView('pdf.inventory_requests_single_pdf', ['inventoryRequest' => $inventoryRequest->load(['requester', 'approver', 'item'])])->setPaper('a4', 'portrait');
        return $pdf->stream('inventory_request_' . $inventoryRequest->id . '.pdf');
    }

    public function generatePdf(Request $request)
    {
        $query = InventoryRequest::with(['requester', 'approver', 'item']);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereHas('item', fn($q) => $q->where('name', 'like', "%$search%"))
                  ->orWhereHas('requester', fn($q) => $q->where('first_name', 'like', "%$search%")->orWhere('last_name', 'like', "%$search%"));
        }

        $inventoryRequests = $query->get();

        $pdf = Pdf::loadView('pdf.inventory_requests_pdf', ['inventoryRequests' => $inventoryRequests])->setPaper('a4', 'landscape');
        return $pdf->stream('inventory_requests.pdf');
    }

    public function create()
    {
        $staffList = \App\Models\Staff::all();
        $inventoryItems = \App\Models\InventoryItem::all();

        return Inertia::render('Admin/InventoryRequests/Create', [
            'staffList' => $staffList,
            'inventoryItems' => $inventoryItems,
        ]);
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
        $staffList = \App\Models\Staff::all();
        $inventoryItems = \App\Models\InventoryItem::all();

        return Inertia::render('Admin/InventoryRequests/Edit', [
            'inventoryRequest' => $inventoryRequest->load(['requester', 'approver', 'item']),
            'staffList' => $staffList,
            'inventoryItems' => $inventoryItems,
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

    public function show(InventoryRequest $inventoryRequest)
    {
        return Inertia::render('Admin/InventoryRequests/Show', [
            'inventoryRequest' => $inventoryRequest->load(['requester', 'approver', 'item']),
        ]);
    }
}
