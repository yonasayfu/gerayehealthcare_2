<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InventoryTransaction>
 */
class InventoryTransactionFactory extends Factory
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
            'request_id' => \App\Models\InventoryRequest::factory(),
            'from_location' => $this->faker->city,
            'to_location' => $this->faker->city,
            'from_assigned_to_type' => $this->faker->randomElement(['staff', 'patient', null]),
            'from_assigned_to_id' => $this->faker->numberBetween(1, 10),
            'to_assigned_to_type' => $this->faker->randomElement(['staff', 'patient', null]),
            'to_assigned_to_id' => $this->faker->numberBetween(1, 10),
            'quantity' => $this->faker->numberBetween(1, 10),
            'transaction_type' => $this->faker->randomElement(['Issue', 'Return', 'Transfer', 'Maintenance Out', 'Maintenance In', 'Disposal']),
            'performed_by_id' => \App\Models\Staff::factory(),
            'notes' => $this->faker->sentence,
        ];
    }
}
