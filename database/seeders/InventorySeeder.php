<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\Supplier;
use App\Models\InventoryItem;
use App\Models\InventoryRequest;
use App\Models\InventoryTransaction;
use App\Models\InventoryMaintenanceRecord;
use App\Models\InventoryAlert;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('TRUNCATE TABLE inventory_alerts RESTART IDENTITY CASCADE;');
        DB::statement('TRUNCATE TABLE inventory_items RESTART IDENTITY CASCADE;');

        Supplier::factory(5)->create();
        InventoryItem::factory(20)->create();
        InventoryRequest::factory(15)->create();
        InventoryTransaction::factory(30)->create();
        InventoryMaintenanceRecord::factory(10)->create();
        InventoryAlert::factory(8)->create();
    }
}
