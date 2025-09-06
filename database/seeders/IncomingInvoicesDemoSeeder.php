<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Patient;
use App\Models\VisitService;
use App\Services\InvoiceService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IncomingInvoicesDemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // Patient A: has completed visits with NO invoices (should appear in Incoming)
            $patientA = Patient::factory()->create([
                'full_name' => 'Incoming Demo Patient',
            ]);

            $visitsA = VisitService::factory()
                ->count(3)
                ->state(function () use ($patientA) {
                    return [
                        'patient_id' => $patientA->id,
                        'status' => 'Completed',
                        'scheduled_at' => now()->subDays(rand(3, 10)),
                    ];
                })
                ->create();

            // Ensure no invoice items exist for A
            InvoiceItem::whereIn('visit_service_id', $visitsA->pluck('id'))->delete();

            // Patient B: has completed visits that ARE already invoiced (should appear in Invoices)
            $patientB = Patient::factory()->create([
                'full_name' => 'Invoiced Demo Patient',
            ]);

            $visitsB = VisitService::factory()
                ->count(3)
                ->state(function () use ($patientB) {
                    return [
                        'patient_id' => $patientB->id,
                        'status' => 'Completed',
                        'scheduled_at' => now()->subDays(rand(1, 5)),
                    ];
                })
                ->create();

            // Create an invoice for patient B using the existing service for accurate totals
            /** @var InvoiceService $svc */
            $svc = app(InvoiceService::class);
            $invoiceB = $svc->create([
                'patient_id' => $patientB->id,
                'visit_ids' => $visitsB->pluck('id')->all(),
                'status' => 'Issued',
            ]);

            // Also create an older Paid invoice for variety
            $olderVisits = VisitService::factory()
                ->count(2)
                ->state(function () use ($patientB) {
                    return [
                        'patient_id' => $patientB->id,
                        'status' => 'Completed',
                        'scheduled_at' => now()->subDays(rand(15, 25)),
                    ];
                })
                ->create();

            $invoiceOld = $svc->create([
                'patient_id' => $patientB->id,
                'visit_ids' => $olderVisits->pluck('id')->all(),
                'status' => 'Paid',
            ]);
            $invoiceOld->update(['paid_at' => now()->subDays(10)]);
        });
    }
}
