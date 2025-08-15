<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StaffPayout>
 */
class StaffPayoutFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'staff_id' => \App\Models\Staff::factory(),
            'total_amount' => $this->faker->randomFloat(2, 50, 500),
            'payout_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'status' => $this->faker->randomElement(['Paid', 'Pending', 'Failed']),
        ];
    }
}
