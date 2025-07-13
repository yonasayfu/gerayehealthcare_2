<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'full_name' => $this->faker->name,
            'fayda_id' => $this->faker->unique()->numerify('##############'),
            
            'date_of_birth' => $this->faker->date,
            'gender' => $this->faker->randomElement(['Male', 'Female']),
            'address' => $this->faker->address,
            'phone_number' => $this->faker->unique()->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'emergency_contact' => $this->faker->name . ' - ' . $this->faker->phoneNumber,
            'geolocation' => $this->faker->latitude . ',' . $this->faker->longitude,
        ];
    }
}
