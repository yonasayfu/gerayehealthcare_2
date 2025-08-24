<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\Staff;
use App\Models\InventoryItem;
use App\Models\Invoice;
use App\Models\VisitService;

class GlobalSearchService
{
    /**
     * Perform global search across multiple entities
     */
    public function search(string $query): array
    {
        if (empty($query) || strlen($query) < 2) {
            return [];
        }

        $results = [];

        // Search Patients
        $results = array_merge($results, $this->searchPatients($query));

        // Search Staff
        $results = array_merge($results, $this->searchStaff($query));

        // Search Inventory Items
        $results = array_merge($results, $this->searchInventoryItems($query));

        // Search Invoices
        $results = array_merge($results, $this->searchInvoices($query));

        // Search Visit Services
        $results = array_merge($results, $this->searchVisitServices($query));

        return $results;
    }

    private function searchPatients(string $query): array
    {
        $patients = Patient::where('full_name', 'ILIKE', '%' . $query . '%')
            ->orWhere('patient_code', 'ILIKE', '%' . $query . '%')
            ->limit(5)
            ->get();

        return $patients->map(function ($patient) {
            return [
                'type' => 'Patient',
                'title' => $patient->full_name,
                'description' => 'Code: ' . ($patient->patient_code ?? 'N/A'),
                'url' => route('admin.patients.show', $patient->id)
            ];
        })->toArray();
    }

    private function searchStaff(string $query): array
    {
        $staff = Staff::where('first_name', 'ILIKE', '%' . $query . '%')
            ->orWhere('last_name', 'ILIKE', '%' . $query . '%')
            ->orWhere('email', 'ILIKE', '%' . $query . '%')
            ->limit(5)
            ->get();

        return $staff->map(function ($s) {
            return [
                'type' => 'Staff',
                'title' => $s->first_name . ' ' . $s->last_name,
                'description' => 'Position: ' . ($s->position ?? 'N/A'),
                'url' => route('admin.staff.show', $s->id)
            ];
        })->toArray();
    }

    private function searchInventoryItems(string $query): array
    {
        $items = InventoryItem::where('name', 'ILIKE', '%' . $query . '%')
            ->limit(5)
            ->get();

        return $items->map(function ($item) {
            return [
                'type' => 'Inventory Item',
                'title' => $item->name,
                'description' => 'Status: ' . ($item->status ?? 'N/A'),
                'url' => route('admin.inventory-items.show', $item->id)
            ];
        })->toArray();
    }

    private function searchInvoices(string $query): array
    {
        $invoices = Invoice::with('patient')
            ->where('invoice_number', 'ILIKE', '%' . $query . '%')
            ->limit(5)
            ->get();

        return $invoices->map(function ($invoice) {
            return [
                'type' => 'Invoice',
                'title' => 'Invoice #' . $invoice->invoice_number,
                'description' => 'Patient: ' . ($invoice->patient->full_name ?? 'N/A'),
                'url' => route('admin.invoices.show', $invoice->id)
            ];
        })->toArray();
    }

    private function searchVisitServices(string $query): array
    {
        $visits = VisitService::with(['patient', 'staff'])
            ->whereHas('patient', function ($q) use ($query) {
                $q->where('full_name', 'ILIKE', '%' . $query . '%');
            })
            ->limit(5)
            ->get();

        return $visits->map(function ($visit) {
            return [
                'type' => 'Visit Service',
                'title' => 'Visit for ' . ($visit->patient->full_name ?? 'N/A'),
                'description' => 'Status: ' . $visit->status,
                'url' => route('admin.visit-services.show', $visit->id)
            ];
        })->toArray();
    }
}
