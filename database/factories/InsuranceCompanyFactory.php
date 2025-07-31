<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InsuranceCompany>
 */
class InsuranceCompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'name_amharic' => $this->faker->company,
            'contact_person' => $this->faker->name,
            'contact_email' => $this->faker->unique()->safeEmail,
            'contact_phone' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'address_amharic' => $this->faker->address,
        ];
    }
}
