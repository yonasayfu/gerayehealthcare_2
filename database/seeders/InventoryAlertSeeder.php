<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InventoryAlert;
use App\Models\InventoryItem;
use Carbon\Carbon;

class InventoryAlertSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure inventory items exist
        if (InventoryItem::count() === 0) {
            InventoryItem::factory()->count(10)->create();
        }

        $itemIds = InventoryItem::pluck('id');

        foreach ($itemIds as $itemId) {
            // Create 1-3 random alerts for each item
            for ($i = 0; $i < rand(1, 3); $i++) {
                InventoryAlert::create([
                    'item_id' => $itemId,
                    'alert_type' => collect(['Low Stock', 'Expired', 'Maintenance Due', 'Damaged'])->random(),
                    'threshold_value' => rand(5, 20),
                    'message' => $this->faker()->sentence(),
                    'is_active' => rand(0, 1),
                    'triggered_at' => Carbon::now()->subDays(rand(1, 30)),
                ]);
            }
        }
    }

    protected function faker()
    {
        return \Faker\Factory::create();
    }
}
