<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InventoryAlert;
use App\Models\InventoryItem;
use Carbon\Carbon;
use Faker\Factory as Faker;

class InventoryAlertSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        if (InventoryItem::count() === 0) {
            InventoryItem::factory()->count(3)->create(); // Limit items to 3
        }

        $itemIds = InventoryItem::pluck('id');

        foreach ($itemIds as $itemId) {
            // One Active alert
            InventoryAlert::create([
                'item_id' => $itemId,
                'alert_type' => 'Low Stock',
                'threshold_value' => $faker->numberBetween(5, 25),
                'message' => 'Stock below reorder level. Please restock soon.',
                'is_active' => true,
                'triggered_at' => Carbon::now()->subDays($faker->numberBetween(1, 10)),
            ]);

            // One Resolved alert
            InventoryAlert::create([
                'item_id' => $itemId,
                'alert_type' => $faker->randomElement(['Maintenance Due', 'Warranty Expiry', 'Overdue Return']),
                'threshold_value' => $faker->numberBetween(5, 25),
                'message' => 'Previously flagged issue has been resolved.',
                'is_active' => false,
                'triggered_at' => Carbon::now()->subDays($faker->numberBetween(11, 45)),
            ]);
        }
    }
}
