<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UnifiedDemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            UserSeeder::class,
            StaffSeeder::class,
            PatientSeeder::class,
            ServiceSeeder::class,
            VisitSeeder::class,
            InvoiceSeeder::class,
        ]);
    }
}
