<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MarketingPlatform>
 */
class MarketingPlatformFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->company() . ' ' . $this->faker->randomElement(['Ads', 'Platform']),
            'api_endpoint' => $this->faker->url(),
            'api_credentials' => $this->faker->sha256(),
            'is_active' => $this->faker->boolean(80),
        ];
    }
}
