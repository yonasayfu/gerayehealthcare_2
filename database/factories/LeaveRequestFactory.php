<?php

namespace Database\Factories;

use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeaveRequestFactory extends Factory
{
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('+1 week', '+1 month');
        $endDate = fake()->dateTimeBetween($startDate, (clone $startDate)->modify('+5 days'));

        return [
            'staff_id' => Staff::inRandomOrder()->first()->id,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'reason' => fake()->sentence(),
            'status' => fake()->randomElement(['Pending', 'Approved', 'Denied']),
            'admin_notes' => fake()->optional()->sentence(),
        ];
    }
}