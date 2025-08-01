<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class EventRecommendationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_id' => \App\Models\Event::factory(),
            'source_channel' => $this->faker->randomElement(['Google Form', 'Facebook', 'Telegram', 'Poster']),
            'recommended_by_name' => $this->faker->name,
            'patient_name' => $this->faker->name,
            'age' => $this->faker->numberBetween(1, 100),
            'gender' => $this->faker->randomElement(['Male', 'Female']),
            'phone_number' => $this->faker->phoneNumber,
            'region' => $this->faker->city,
            'woreda' => $this->faker->word,
            'reason' => $this->faker->sentence,
            'notes' => $this->faker->paragraph,
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
        ];
    }
}
