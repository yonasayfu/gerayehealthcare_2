<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\AdditionalExportConfigs;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Inertia\Inertia;
use League\Csv\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class InventoryTransactionController extends Controller
{
    use ExportableTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = InventoryTransaction::with(['item', 'performedBy']);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('transaction_type', 'like', "%$search%")
                  ->orWhereHas('item', fn($q) => $q->where('name', 'like', "%$search%"));
        }

        $inventoryTransactions = $query->paginate($request->input('per_page', 5));

        return Inertia::render('Admin/InventoryTransactions/Index', [
            'inventoryTransactions' => $inventoryTransactions,
            'filters' => $request->all('search'),
        ]);
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, InventoryTransaction::class, AdditionalExportConfigs::getInventoryTransactionConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, InventoryTransaction::class, AdditionalExportConfigs::getInventoryTransactionConfig());
    }

    public function printSingle(InventoryTransaction $inventoryTransaction)
    {
        return $this->handlePrintSingle($inventoryTransaction, AdditionalExportConfigs::getInventoryTransactionConfig());
    }

    public function generatePdf(Request $request)
    {
        return $this->handlePrintCurrent($request, InventoryTransaction::class, AdditionalExportConfigs::getInventoryTransactionConfig());
    }

    public function show(InventoryTransaction $inventoryTransaction)
    {
        return Inertia::render('Admin/InventoryTransactions/Show', [
            'inventoryTransaction' => $inventoryTransaction->load(['item', 'performedBy']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:inventory_items,id',
            'request_id' => 'nullable|exists:inventory_requests,id',
            'from_location' => 'nullable|string|max:255',
            'to_location' => 'nullable|string|max:255',
            'from_assigned_to_type' => 'nullable|string|max:50',
            'from_assigned_to_id' => 'nullable|integer',
            'to_assigned_to_type' => 'nullable|string|max:50',
            'to_assigned_to_id' => 'nullable|integer',
            'quantity' => 'required|integer',
            'transaction_type' => 'required|string|max:50',
            'performed_by_id' => 'required|exists:staff,id',
            'notes' => 'nullable|string',
        ]);

        InventoryTransaction::create($validated);

        return redirect()->route('admin.inventory-transactions.index')->with('success', 'Inventory transaction recorded successfully.');
    }
}
