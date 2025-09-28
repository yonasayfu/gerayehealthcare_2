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
            'page_url' => $this->faker->unique()->url(),
            'template_used' => $this->faker->randomElement(['universal', 'lead-gen', 'promo', 'webinar']),
            'language' => $this->faker->randomElement(['en', 'es', 'fr', 'de', 'zh']),
            'form_fields' => [
                'name' => true,
                'email' => true,
                'phone' => $this->faker->boolean(50),
            ],
            'conversion_goal' => $this->faker->randomElement(['Lead', 'Signup', 'Download', 'Call']),
            'views' => $this->faker->numberBetween(0, 1000),
            'submissions' => $this->faker->numberBetween(0, 500),
            'conversion_rate' => $this->faker->randomFloat(2, 0, 1),
            'is_active' => $this->faker->boolean(),
            'notes' => $this->faker->sentence(),
        ];
    }
}
