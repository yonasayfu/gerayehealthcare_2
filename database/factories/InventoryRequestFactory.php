<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InventoryRequest>
 */
class InventoryRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'requester_id' => \App\Models\Staff::all()->random()->id,
            'approver_id' => \App\Models\Staff::all()->random()->id,
            'item_id' => \App\Models\InventoryItem::factory(),
            'quantity_requested' => $this->faker->numberBetween(1, 5),
            'quantity_approved' => $this->faker->numberBetween(1, 5),
            'reason' => $this->faker->sentence,
            'status' => $this->faker->randomElement(['Pending', 'Approved', 'Rejected', 'Fulfilled', 'Partially Fulfilled']),
            'priority' => $this->faker->randomElement(['Low', 'Normal', 'High', 'Urgent']),
            'needed_by_date' => Carbon::instance($this->faker->dateTimeBetween('+1 day', '+1 month'))->format('Y-m-d'),
            'approved_at' => Carbon::instance($this->faker->dateTimeBetween('-1 month', 'now')),
            'fulfilled_at' => Carbon::instance($this->faker->dateTimeBetween('-1 month', 'now')),
        ];
    }
}
