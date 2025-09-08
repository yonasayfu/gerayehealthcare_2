<?php

namespace Database\Seeders;

use App\Models\InventoryMaintenanceRecord;
use Illuminate\Database\Seeder;

class InventoryMaintenanceRecordSeeder extends Seeder
{
    public function run(): void
    {
        InventoryMaintenanceRecord::factory()->count(20)->create();
    }
}
