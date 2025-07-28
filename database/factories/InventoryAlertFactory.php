<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InventoryAlert>
 */
class InventoryAlertFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item_id' => \App\Models\InventoryItem::factory(),
            'alert_type' => $this->faker->randomElement(['Low Stock', 'Maintenance Due', 'Warranty Expiry', 'Overdue Return']),
            'threshold_value' => $this->faker->word,
            'message' => $this->faker->sentence,
            'is_active' => true,
            'triggered_at' => Carbon::instance($this->faker->dateTimeBetween('-1 month', 'now'))->format('Y-m-d H:i:s'),
        ];
    }
}