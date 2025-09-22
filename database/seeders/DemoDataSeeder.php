<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            TestUsersSeeder::class,
            StaffScenariosSeeder::class,
            PatientScenariosSeeder::class,
            VisitScenariosSeeder::class,
            InvoiceScenariosSeeder::class,
            InventoryScenariosSeeder::class,
            // Add other scenario seeders here
        ]);
    }
}