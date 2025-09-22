<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use App\Models\InventoryAlert;
use App\Models\InventoryMaintenanceRecord;
use App\Models\Staff;
use Illuminate\Database\Seeder;

class InventoryScenariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staffUser = Staff::where('email', 'staff@gerayehealthcare.com')->first();
        $adminUser = Staff::where('email', 'admin@gerayehealthcare.com')->first();

        // Create an item assigned to the staff user
        if ($staffUser) {
            InventoryItem::factory()->create([
                'assigned_to_type' => 'staff',
                'assigned_to_id' => $staffUser->id,
            ]);
        }

        // Create an item assigned to the admin user
        if ($adminUser) {
            InventoryItem::factory()->create([
                'assigned_to_type' => 'staff',
                'assigned_to_id' => $adminUser->id,
            ]);
        }

        // Create an item in maintenance
        $itemInMaintenance = InventoryItem::factory()->inMaintenance()->create();
        InventoryMaintenanceRecord::factory()->create([
            'item_id' => $itemInMaintenance->id,
            'status' => 'In Progress',
        ]);

        // Create an item with a low stock alert
        $lowStockItem = InventoryItem::factory()->lowStock()->create();
        InventoryAlert::factory()->create([
            'item_id' => $lowStockItem->id,
            'alert_type' => 'Low Stock',
            'is_active' => true,
        ]);
    }
}
