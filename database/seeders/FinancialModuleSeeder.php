<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Patient;
use App\Models\Staff;
use App\Models\StaffPayout;
use App\Models\VisitService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class FinancialModuleSeeder extends Seeder
{
    /**
     * Seed invoices, invoice line items, and staff payouts.
     */
    public function run(): void
    {
        $patients = Patient::orderBy('id')->take(5)->get();
        if ($patients->isEmpty()) {
            $patients = Patient::factory()->count(5)->create();
        }

        $visitServices = VisitService::orderBy('id')->take(5)->get();
        if ($visitServices->isEmpty()) {
            $visitServices = VisitService::factory()->count(5)->create();
        }

        foreach ($patients as $index => $patient) {
            $visit = $visitServices[$index % $visitServices->count()];
            $invoice = Invoice::factory()->create([
                'patient_id' => $patient->id,
                'invoice_date' => Carbon::now()->subDays($index * 3),
                'due_date' => Carbon::now()->addDays(15 + $index),
                'status' => $index % 2 === 0 ? 'Paid' : 'Pending',
            ]);

            InvoiceItem::factory()->count(2)->create([
                'invoice_id' => $invoice->id,
                'visit_service_id' => $visit->id,
            ]);
        }

        $staffMembers = Staff::orderBy('id')->take(5)->get();
        if ($staffMembers->isEmpty()) {
            $staffMembers = Staff::factory()->count(5)->create();
        }

        foreach ($staffMembers as $index => $staff) {
            $payout = StaffPayout::factory()->create([
                'staff_id' => $staff->id,
                'payout_date' => Carbon::now()->subWeeks($index),
                'status' => $index % 2 === 0 ? 'Paid' : 'Pending',
                'total_amount' => 500 + ($index * 75),
            ]);

            $relatedVisits = $visitServices->shuffle()->take(2)->pluck('id');
            if ($relatedVisits->isNotEmpty()) {
                $payout->visitServices()->sync($relatedVisits);
            }
        }
    }
}
