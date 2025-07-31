<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExchangeRate>
 */
class ExchangeRateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'currency_code' => $this->faker->currencyCode,
            'rate_to_etb' => $this->faker->randomFloat(4, 0, 100),
            'source' => $this->faker->word,
            'date_effective' => $this->faker->date,
        ];
    }
}
