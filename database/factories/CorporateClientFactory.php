<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CorporateClient>
 */
class CorporateClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_name' => $this->faker->company,
            'contact_person' => $this->faker->name,
            'contact_email' => $this->faker->unique()->safeEmail,
            'contact_phone' => $this->faker->phoneNumber,
            'tin_number' => $this->faker->numerify('##########'),
            'trade_license_number' => $this->faker->numerify('######'),
            'address' => $this->faker->address,
        ];
    }
}
