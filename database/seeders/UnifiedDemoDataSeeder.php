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
            CoreUserSeeder::class,
            ClinicalModuleSeeder::class,
            TaskModuleSeeder::class,
            CommunicationModuleSeeder::class,
            InventoryModuleSeeder::class,
            FinancialModuleSeeder::class,
            MarketingModuleSeeder::class,
            InsuranceModuleSeeder::class,
            PartnershipModuleSeeder::class,
            EventsModuleSeeder::class,
        ]);
    }
}
