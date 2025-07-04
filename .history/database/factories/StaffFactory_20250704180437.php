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
            // Corrected to match the database schema
            'full_name' => $this->faker->name(),
            'id_number' => $this->faker->unique()->numerify('ID######'),
            'email' => $this->faker->unique()->safeEmail(),
            'phone_number' => $this->faker->phoneNumber(),
            
            // This is the new field we added to fix the seeder error
            'role' => $this->faker->randomElement(['Nurse', 'Therapist', 'Caregiver', 'Admin']),
            
            // These fields align with your schema
            'department' => $this->faker->randomElement(['HR', 'Finance', 'Medical', 'Operations']),
            'status' => $this->faker->randomElement(['Active', 'Inactive']),
            'photo' => 'images/staff/placeholder.jpg',
        ];
    }
}