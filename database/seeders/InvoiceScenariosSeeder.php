<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Services\Invoice\InvoiceService;
use Illuminate\Database\Seeder;

class InvoiceScenariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(InvoiceService $invoiceService): void
    {
        $patientsWithCompletedVisits = Patient::whereHas('visitServices', function ($query) {
            $query->where('status', 'Completed')->where('is_invoiced', false);
        })->with(['visitServices' => function ($query) {
            $query->where('status', 'Completed')->where('is_invoiced', false);
        }])->get();

        if ($patientsWithCompletedVisits->isEmpty()) {
            return;
        }

        // Create a paid invoice for one patient
        $patient1 = $patientsWithCompletedVisits->first();
        if ($patient1) {
            $visitIds1 = $patient1->visitServices->pluck('id')->all();
            $invoiceService->create([
                'patient_id' => $patient1->id,
                'visit_ids' => $visitIds1,
                'status' => 'Paid',
            ]);
        }


        // Create a pending invoice for another patient
        $patient2 = $patientsWithCompletedVisits->last();
        if ($patient2 && $patient1->id !== $patient2->id) {
            $visitIds2 = $patient2->visitServices->pluck('id')->all();
            $invoiceService->create([
                'patient_id' => $patient2->id,
                'visit_ids' => $visitIds2,
                'status' => 'Pending',
            ]);
        }
    }
}
