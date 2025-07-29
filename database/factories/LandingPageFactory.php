<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LandingPage>
 */
class LandingPageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'page_title' => $this->faker->sentence(3),
            'page_url' => $this->faker->url,
            'conversion_rate' => $this->faker->randomFloat(2, 0, 1),
            'notes' => $this->faker->paragraph,
        ];
    }
}
