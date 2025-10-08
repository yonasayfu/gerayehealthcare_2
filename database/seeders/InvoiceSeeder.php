<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\VisitService;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $visits = VisitService::all();

        foreach ($visits as $visit) {
            Invoice::factory()->create([
                'patient_id' => $visit->patient_id,
                'invoice_date' => $visit->scheduled_at,
                'due_date' => now()->addDays(30),
            ]);
        }
    }
}
