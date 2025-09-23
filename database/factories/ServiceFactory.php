<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word().' Service',
            'description' => $this->faker->sentence(),
            'category' => $this->faker->randomElement(['Medical', 'Therapy', 'Consultation', 'Nursing']),
            'price' => $this->faker->randomFloat(2, 50, 1000),
            'duration' => $this->faker->numberBetween(15, 120),
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
        ];
    }
}
