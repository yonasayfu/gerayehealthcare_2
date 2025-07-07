<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VisitService>
 */
class VisitServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $scheduled = $this->faker->dateTimeBetween('+1 day', '+2 months');
        $checkIn = $this->faker->optional(0.7)->dateTimeBetween($scheduled, (clone $scheduled)->modify('+15 minutes'));

        return [
            'patient_id' => Patient::inRandomOrder()->first()->id,
            'staff_id' => Staff::inRandomOrder()->first()->id,
            'scheduled_at' => $scheduled,
            'check_in_time' => $checkIn,
            'check_out_time' => $checkIn ? $this->faker->optional(0.9)->dateTimeBetween($checkIn, (clone $checkIn)->modify('+2 hours')) : null,
            'visit_notes' => $this->faker->optional()->paragraph,
            'status' => $this->faker->randomElement(['Pending', 'Completed', 'Cancelled']),
        ];
    }
}