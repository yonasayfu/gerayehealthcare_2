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
            InventoryItem::factory()->count(20)->create();
        }

        $itemIds = InventoryItem::pluck('id');

        foreach ($itemIds as $itemId) {
            for ($i = 0; $i < rand(1, 4); $i++) {
                InventoryAlert::create([
                    'item_id' => $itemId,
                    'alert_type' => $faker->randomElement(['Low Stock', 'Expired', 'Maintenance Due', 'Damaged']),
                    'threshold_value' => $faker->numberBetween(5, 25),
                    'message' => $faker->sentence,
                    'is_active' => $faker->boolean(80),
                    'triggered_at' => Carbon::now()->subDays($faker->numberBetween(1, 45)),
                    'due_date' => Carbon::now()->addDays($faker->numberBetween(7, 60)),
                ]);
            }
        }
    }
}
