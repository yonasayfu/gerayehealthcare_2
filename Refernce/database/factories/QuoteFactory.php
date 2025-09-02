<?php

namespace Database\Factories;

use App\Models\Quote;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Quote> */
class QuoteFactory extends Factory
{
    protected $model = Quote::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'text' => $this->faker->sentence(12),
            'author' => $this->faker->name(),
            'language' => $this->faker->randomElement(['en', '']),
            'pinned' => false,
            'priority' => $this->faker->optional()->numberBetween(0, 10),
        ];
    }
}
