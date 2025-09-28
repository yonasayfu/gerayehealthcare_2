<?php

namespace Database\Factories;

use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InventoryMaintenanceRecord>
 */
class InventoryMaintenanceRecordFactory extends Factory
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
            'scheduled_date' => Carbon::instance($this->faker->dateTimeBetween('-1 month', '+1 month'))->format('Y-m-d'),
            'actual_date' => Carbon::instance($this->faker->dateTimeBetween('-2 months', 'now'))->format('Y-m-d'),
            'performed_by_staff_id' => Staff::inRandomOrder()->first()->id,
            'cost' => $this->faker->randomFloat(2, 10, 1000),
            'description' => $this->faker->sentence,
            'next_due_date' => Carbon::instance($this->faker->dateTimeBetween('now', '+6 months'))->format('Y-m-d'),
            'status' => $this->faker->randomElement(['Scheduled', 'Completed', 'Overdue', 'Cancelled']),
        ];
    }
}
