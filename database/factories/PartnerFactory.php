<?php

namespace Database\Factories;

use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Partner>
 */
class PartnerFactory extends Factory
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
            'type' => $this->faker->randomElement(['Corporate', 'NGO', 'School', 'Bank', 'Government Agency']),
            'contact_person' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'engagement_status' => $this->faker->randomElement(['Prospect', 'Active', 'Inactive']),
            'account_manager_id' => Staff::inRandomOrder()->first()->id ?? null,
            'notes' => $this->faker->sentence,
        ];
    }
}
