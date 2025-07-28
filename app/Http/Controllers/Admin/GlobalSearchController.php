<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Patient;
use App\Models\Staff;
use App\Models\InventoryItem;
use App\Models\Invoice;
use App\Models\VisitService;

class GlobalSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q');
        $results = [];

        if (empty($query)) {
            return response()->json($results);
        }

        // Search Patients
        $patients = Patient::where('full_name', 'ILIKE', '%' . $query . '%')
                           ->orWhere('patient_code', 'ILIKE', '%' . $query . '%')
                           ->limit(5)->get();
        foreach ($patients as $patient) {
            $results[] = [
                'type' => 'Patient',
                'title' => $patient->full_name,
                'description' => 'Patient Code: ' . $patient->patient_code,
                'url' => route('admin.patients.show', $patient->id),
            ];
        }

        // Search Staff
        $staff = Staff::where('first_name', 'ILIKE', '%' . $query . '%')
                      ->orWhere('last_name', 'ILIKE', '%' . $query . '%')
                      ->orWhere('email', 'ILIKE', '%' . $query . '%')
                      ->limit(5)->get();
        foreach ($staff as $s) {
            $results[] = [
                'type' => 'Staff',
                'title' => $s->first_name . ' ' . $s->last_name,
                'description' => 'Email: ' . $s->email,
                'url' => route('admin.staff.show', $s->id), // Assuming a staff show route
            ];
        }

        // Search Inventory Items
        $inventoryItems = InventoryItem::where('name', 'ILIKE', '%' . $query . '%')
                                       ->orWhere('serial_number', 'ILIKE', '%' . $query . '%')
                                       ->limit(5)->get();
        foreach ($inventoryItems as $item) {
            $results[] = [
                'type' => 'Inventory Item',
                'title' => $item->name,
                'description' => 'Serial: ' . ($item->serial_number ?? 'N/A'),
                'url' => route('admin.inventory-items.show', $item->id), // Assuming an inventory item show route
            ];
        }

        // Search Invoices (by patient name or invoice ID)
        $invoices = Invoice::with('patient')
                           ->where(function($q) use ($query) {
                               $q->where('id', 'ILIKE', '%' . $query . '%')
                                 ->orWhereHas('patient', fn($pq) => $pq->where('full_name', 'ILIKE', '%' . $query . '%'));
                           })
                           ->limit(5)->get();
        foreach ($invoices as $invoice) {
            $results[] = [
                'type' => 'Invoice',
                'title' => 'Invoice #' . $invoice->id,
                'description' => 'Patient: ' . ($invoice->patient->full_name ?? 'N/A') . ' | Total: ' . $invoice->grand_total,
                'url' => route('admin.invoices.show', $invoice->id), // Assuming an invoice show route
            ];
        }

        // Search Visit Services (by patient name or staff name)
        $visitServices = VisitService::with(['patient', 'staff'])
                                     ->where(function($q) use ($query) {
                                         $q->whereHas('patient', fn($pq) => $pq->where('full_name', 'ILIKE', '%' . $query . '%'))
                                           ->orWhereHas('staff', fn($sq) => $sq->where('first_name', 'ILIKE', '%' . $query . '%')->orWhere('last_name', 'ILIKE', '%' . $query . '%'));
                                     })
                                     ->limit(5)->get();
        foreach ($visitServices as $visit) {
            $results[] = [
                'type' => 'Visit Service',
                'title' => 'Visit for ' . ($visit->patient->full_name ?? 'N/A'),
                'description' => 'Staff: ' . ($visit->staff ? $visit->staff->first_name . ' ' . $visit->staff->last_name : 'N/A') . ' | Status: ' . $visit->status,
                'url' => route('admin.visit-services.show', $visit->id), // Assuming a visit service show route
            ];
        }

        return response()->json($results);
    }
}