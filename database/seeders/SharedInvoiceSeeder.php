<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SharedInvoice;

class SharedInvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Generate shared invoices with related invoice, partner and staff via factory
        SharedInvoice::factory(20)->create();
    }
}
