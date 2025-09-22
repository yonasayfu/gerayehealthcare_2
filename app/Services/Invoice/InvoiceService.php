<?php

namespace App\Services\Invoice;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\VisitService;
use Illuminate\Http\Request;

class InvoiceService
{
    public function getAll(Request $request)
    {
        $query = Invoice::with(['patient:id,full_name,email,phone_number,address', 'items.visitService.service:id,name']);

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhereHas('patient', function ($subQuery) use ($search) {
                        $subQuery->where('full_name', 'like', "%{$search}%");
                    });
            });
        }

        // Apply sorting
        $sortBy = $request->get('sort', 'id');
        $sortOrder = $request->get('direction', 'desc');

        // Ensure sort column is valid
        $validSortColumns = ['id', 'invoice_date', 'due_date', 'status', 'grand_total'];
        if (!in_array($sortBy, $validSortColumns)) {
            $sortBy = 'id';
        }

        $query->orderBy($sortBy, $sortOrder);

        // Apply pagination
        $perPage = $request->get('per_page', 10);

        return $query->paginate($perPage)->withQueryString();
    }

    public function getById(int $id): Invoice
    {
        return Invoice::with(['patient:id,full_name,email,phone_number,address', 'items.visitService.service:id,name'])
            ->findOrFail($id);
    }

    public function create(array $data): Invoice
    {
        $invoice = Invoice::create([
            'patient_id' => $data['patient_id'],
            'status' => $data['status'],
            'invoice_date' => now(),
            'due_date' => now()->addDays(30), // Example due date
            'subtotal' => 0,
            'tax_amount' => 0,
            'grand_total' => 0, // Will be calculated after items are added
            'amount' => 0,
        ]);

        $totalAmount = 0;
        foreach ($data['visit_ids'] as $visitId) {
            $visitService = VisitService::find($visitId);
            if ($visitService) {
                $itemAmount = $visitService->price ?? 100; // Example price
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'visit_service_id' => $visitId,
                    'description' => 'Service for visit ' . $visitId,
                    'quantity' => 1,
                    'cost' => $itemAmount,
                ]);
                $totalAmount += $itemAmount;
            }
        }

        $invoice->update([
            'subtotal' => $totalAmount,
            'grand_total' => $totalAmount,
            'amount' => $totalAmount,
        ]);

        return $invoice;
    }

    public function update(int $id, array $data): Invoice
    {
        $invoice = $this->getById($id);
        $invoice->update($data);

        return $invoice;
    }

    public function delete(int $id): void
    {
        $invoice = $this->getById($id);
        $invoice->delete();
    }
}
