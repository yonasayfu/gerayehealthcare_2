<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Inertia\Inertia;
use League\Csv\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class InventoryTransactionController extends Controller
{
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

        $inventoryTransactions = $query->paginate(10);

        return Inertia::render('Admin/InventoryTransactions/Index', [
            'inventoryTransactions' => $inventoryTransactions,
            'filters' => $request->all('search'),
        ]);
    }

    public function export(): StreamedResponse
    {
        $inventoryTransactions = InventoryTransaction::with(['item', 'performedBy'])->get();

        $csv = Writer::createFromString('');
        $csv->insertOne(['ID', 'Item', 'Request ID', 'From Location', 'To Location', 'From Assigned To Type', 'From Assigned To ID', 'To Assigned To Type', 'To Assigned To ID', 'Quantity', 'Transaction Type', 'Performed By', 'Notes', 'Created At']);

        foreach ($inventoryTransactions as $transaction) {
            $csv->insertOne([
                $transaction->id,
                $transaction->item->name,
                $transaction->request_id,
                $transaction->from_location,
                $transaction->to_location,
                $transaction->from_assigned_to_type,
                $transaction->from_assigned_to_id,
                $transaction->to_assigned_to_type,
                $transaction->to_assigned_to_id,
                $transaction->quantity,
                $transaction->transaction_type,
                $transaction->performedBy->first_name . ' ' . $transaction->performedBy->last_name,
                $transaction->notes,
                $transaction->created_at,
            ]);
        }

        return response()->streamDownload(function () use ($csv) {
            echo $csv->toString();
        }, 'inventory_transactions.csv', [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="inventory_transactions.csv"',
        ]);
    }

    public function printAll(Request $request)
    {
        $query = InventoryTransaction::with(['item', 'performedBy']);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('transaction_type', 'like', "%$search%")
                  ->orWhereHas('item', fn($q) => $q->where('name', 'like', "%$search%"));
        }

        $inventoryTransactions = $query->get();

        return Inertia::render('Admin/InventoryTransactions/PrintAll', [
            'inventoryTransactions' => $inventoryTransactions,
        ]);
    }

    public function printSingle(InventoryTransaction $inventoryTransaction)
    {
        return Inertia::render('Admin/InventoryTransactions/PrintSingle', [
            'inventoryTransaction' => $inventoryTransaction->load(['item', 'performedBy']),
        ]);
    }

    public function generatePdf(Request $request)
    {
        $query = InventoryTransaction::with(['item', 'performedBy']);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('transaction_type', 'like', "%$search%")
                  ->orWhereHas('item', fn($q) => $q->where('name', 'like', "%$search%"));
        }

        $inventoryTransactions = $query->get();

        $pdf = Pdf::loadView('pdf.inventory-transactions', ['inventoryTransactions' => $inventoryTransactions])->setPaper('a4', 'landscape');
        return $pdf->stream('inventory_transactions.pdf');
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