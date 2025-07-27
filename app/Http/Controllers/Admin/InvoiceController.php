<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\VisitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    /**
     * Display a listing of all invoices.
     */
    public function index()
    {
        $invoices = Invoice::with('patient:id,full_name')
            ->orderBy('invoice_date', 'desc')
            ->paginate(15);

        return Inertia::render('Admin/Invoices/Index', [
            'invoices' => $invoices,
        ]);
    }

    /**
     * Show the form for creating a new invoice.
     * Fetches patients and, if a patient is selected, their billable visits.
     */
    public function create(Request $request)
    {
        $billableVisits = [];
        if ($request->has('patient_id')) {
            $billableVisits = VisitService::where('patient_id', $request->patient_id)
                ->where('status', 'Completed')
                ->where('is_invoiced', false)
                ->get();
        }

        return Inertia::render('Admin/Invoices/Create', [
            'patients' => Patient::orderBy('full_name')->get(['id', 'full_name']),
            'selectedPatientId' => $request->input('patient_id'),
            'billableVisits' => $billableVisits,
        ]);
    }

    /**
     * Store a newly created invoice in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'visit_ids' => 'required|array|min:1',
            'visit_ids.*' => 'required|exists:visit_services,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
        ]);

        $visitsToInvoice = VisitService::whereIn('id', $validated['visit_ids'])->get();

        // Use a database transaction for data integrity
        DB::transaction(function () use ($validated, $visitsToInvoice) {
            // 1. Calculate totals
            $subtotal = $visitsToInvoice->sum('cost');
            $taxRate = 0.15; // 15% Tax
            $taxAmount = $subtotal * $taxRate;
            $grandTotal = $subtotal + $taxAmount;

            // 2. Create the main invoice record
            $invoice = Invoice::create([
                'patient_id' => $validated['patient_id'],
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'grand_total' => $grandTotal,
                'status' => 'Pending',
            ]);

            // 3. Create invoice line items
            foreach ($visitsToInvoice as $visit) {
                $invoice->items()->create([
                    'visit_service_id' => $visit->id,
                    'description' => $visit->service_description ?: 'Standard Visit',
                    'cost' => $visit->cost ?? 0.00,
                ]);

                // 4. Mark the visit as having been invoiced
                $visit->update(['is_invoiced' => true]);
            }
        });

        return redirect()->route('admin.invoices.index')->with('success', 'Invoice created successfully.');
    }

    /**
     * Display the specified invoice.
     */
    public function show(Invoice $invoice)
    {
        return Inertia::render('Admin/Invoices/Show', [
            'invoice' => $invoice->load(['patient', 'items.visitService.staff']),
        ]);
    }
}