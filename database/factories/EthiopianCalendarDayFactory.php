<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EthiopianCalendarDay>
 */
class EthiopianCalendarDayFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'gregorian_date' => $this->faker->date(),
            'ethiopian_date' => $this->faker->word,
            'description' => $this->faker->sentence,
            'is_holiday' => $this->faker->boolean,
            'region' => $this->faker->word,
        ];
    }
}
