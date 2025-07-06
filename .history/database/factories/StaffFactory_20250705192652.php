<?php

namespace Database\Factories;

use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

class StaffFactory extends Factory
{
    protected $model = Staff::class;

    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'phone_number' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'date_of_birth' => $this->faker->date(),
            'role' => $this->faker->randomElement(['Nurse', 'Caregiver', 'Admin']),
            'license_number' => $this->faker->numerify('#####'),
            'license_expiry_date' => $this->faker->date(),
            'specialization' => $this->faker->jobTitle,
            'employment_status' => $this->faker->randomElement(['active', 'on_leave', 'terminated']),
            'hire_date' => $this->faker->date(),
            'profile_picture' => 'images/staff/placeholder.jpg',
            'notes' => $this->faker->sentence,
        ];
    }
}