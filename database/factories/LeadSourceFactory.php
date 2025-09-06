<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeadSource>
 */
class LeadSourceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word.' Lead Source',
            'category' => $this->faker->randomElement(['Online', 'Offline', 'Referral', 'Event', 'Other']),
            'description' => $this->faker->sentence,
            'is_active' => $this->faker->boolean,
        ];
    }
}
