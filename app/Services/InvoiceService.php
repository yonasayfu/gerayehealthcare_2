<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Patient;
use App\Models\VisitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceService extends BaseService
{
    public function __construct(Invoice $invoice)
    {
        parent::__construct($invoice);
    }

    protected function applySearch($query, $search)
    {
        $query->where('invoice_number', 'ilike', "%{$search}%")
              ->orWhereHas('patient', function ($q) use ($search) {
                  $q->where('full_name', 'ilike', "%{$search}%");
              });
    }

    public function getAll(Request $request)
    {
        $query = $this->model->with(['patient:id,full_name']);

        if ($request->has('search')) {
            $this->applySearch($query, $request->input('search'));
        }

        if ($request->has('sort')) {
            $direction = $request->input('direction', 'asc');
            $query->orderBy($request->input('sort'), $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($request->input('per_page', 10));
    }

    public function create(array $data): Invoice
    {
        $visitsToInvoice = VisitService::whereIn('id', $data['visit_ids'])->get();

        return DB::transaction(function () use ($data, $visitsToInvoice) {
            // 1. Calculate totals
            $subtotal = $visitsToInvoice->sum('cost');
            $taxRate = 0.15; // 15% Tax
            $taxAmount = $subtotal * $taxRate;
            $grandTotal = $subtotal + $taxAmount;

            // 2. Create the main invoice record
            $invoice = parent::create([
                'patient_id' => $data['patient_id'],
                'invoice_number' => 'INV-' . uniqid(), // Generate unique invoice number
                'invoice_date' => $data['invoice_date'],
                'due_date' => $data['due_date'],
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
            return $invoice;
        });
    }

    public function getById(int $id): Invoice
    {
        return $this->model->with(['patient', 'items.visitService.staff'])->findOrFail($id);
    }
}
