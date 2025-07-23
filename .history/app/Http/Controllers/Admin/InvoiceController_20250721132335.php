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
                    'cost' => $visit->cost,
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

    /**
     * Show the form for editing the specified invoice.
     */
    public function edit(Invoice $invoice)
    {
        $invoice->load('patient', 'visit_services'); // Eager load patient and visit_services

        // Fetch billable visits for the selected patient, excluding those already in this invoice
        $billableVisits = VisitService::where('patient_id', $invoice->patient_id)
            ->where('status', 'Completed')
            ->where('is_invoiced', false)
            ->orWhere(function ($query) use ($invoice) {
                $query->whereHas('invoiceItems', function ($q) use ($invoice) {
                    $q->where('invoice_id', $invoice->id);
                });
            })
            ->get();

        return Inertia::render('Admin/Invoices/Edit', [
            'invoice' => $invoice,
            'patients' => Patient::orderBy('full_name')->get(['id', 'full_name']),
            'billableVisits' => $billableVisits,
            'chapaPublicKey' => config('services.chapa.public_key'), // Pass Chapa public key to frontend
        ]);
    }

    /**
     * Update the specified invoice in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'visit_ids' => 'required|array',
            'visit_ids.*' => 'required|exists:visit_services,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
        ]);

        DB::transaction(function () use ($validated, $invoice) {
            // Mark old visit services as not invoiced if they are removed from this invoice
            $oldVisitServiceIds = $invoice->items->pluck('visit_service_id')->toArray();
            $visitsRemoved = array_diff($oldVisitServiceIds, $validated['visit_ids']);
            VisitService::whereIn('id', $visitsRemoved)->update(['is_invoiced' => false]);

            // Detach old invoice items
            $invoice->items()->delete();

            // Attach new/updated visit services and create new invoice items
            $visitsToInvoice = VisitService::whereIn('id', $validated['visit_ids'])->get();

            $subtotal = $visitsToInvoice->sum('cost');
            $taxRate = 0.15; // 15% Tax
            $taxAmount = $subtotal * $taxRate;
            $grandTotal = $subtotal + $taxAmount;

            $invoice->update([
                'patient_id' => $validated['patient_id'],
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'grand_total' => $grandTotal,
                'status' => 'Pending', // Reset status if items change, or handle based on business logic
            ]);

            foreach ($visitsToInvoice as $visit) {
                $invoice->items()->create([
                    'visit_service_id' => $visit->id,
                    'description' => $visit->service_description ?: 'Standard Visit',
                    'cost' => $visit->cost,
                ]);
                $visit->update(['is_invoiced' => true]);
            }
        });

        return redirect()->route('admin.invoices.index')->with('success', 'Invoice updated successfully.');
    }

    /**
     * Remove the specified invoice from storage.
     */
    public function destroy(Invoice $invoice)
    {
        DB::transaction(function () use ($invoice) {
            // Mark associated visit services as not invoiced
            VisitService::whereIn('id', $invoice->items->pluck('visit_service_id'))
                ->update(['is_invoiced' => false]);

            $invoice->delete();
        });

        return redirect()->route('admin.invoices.index')->with('success', 'Invoice deleted successfully.');
    }
}
