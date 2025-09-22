<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InventoryItem>
 */
class InventoryItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word.' Item',
            'description' => $this->faker->sentence,
            'item_category' => $this->faker->randomElement(['Medical Equipment', 'Office Supplies', 'Diagnostic Tools', 'Furniture']),
            'item_type' => $this->faker->word,
            'serial_number' => $this->faker->unique()->uuid,
            'purchase_date' => $this->faker->date,
            'warranty_expiry' => Carbon::instance($this->faker->dateTimeBetween('+1 month', '+5 years'))->format('Y-m-d'),
            'supplier_id' => \App\Models\Supplier::factory(),
            'assigned_to_type' => $this->faker->randomElement(['staff', 'patient', null]),
            'assigned_to_id' => $this->faker->numberBetween(1, 10), // Assuming staff/patient IDs exist
            'last_maintenance_date' => $this->faker->date,
            'next_maintenance_due' => Carbon::instance($this->faker->dateTimeBetween('+1 week', '+6 months'))->format('Y-m-d'),
            'maintenance_schedule' => $this->faker->sentence,
            'notes' => $this->faker->paragraph,
            'status' => $this->faker->randomElement(['Available', 'In Use', 'Under Maintenance', 'Lost', 'Damaged', 'Retired']),
            'quantity_on_hand' => $this->faker->numberBetween(0, 20), // More likely to be low
            'reorder_level' => $this->faker->numberBetween(5, 15),
        ];
    }

    /**
     * Indicate that the item is under maintenance.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function inMaintenance()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'Under Maintenance',
            ];
        });
    }

    /**
     * Indicate that the item has low stock.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function lowStock()
    {
        return $this->state(function (array $attributes) {
            return [
                'quantity_on_hand' => $this->faker->numberBetween(1, 5),
                'reorder_level' => 10,
            ];
        });
    }
}
