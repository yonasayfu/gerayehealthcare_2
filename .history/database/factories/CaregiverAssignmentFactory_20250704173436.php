<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CaregiverAssignment>
 */
class CaregiverAssignmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $shift_start = $this->faker->dateTimeBetween('-1 month', '+1 month');
        $shift_end = (clone $shift_start)->modify('+8 hours');

        return [
            'patient_id' => Patient::inRandomOrder()->first()->id,
            'staff_id' => Staff::where('role', '!=', 'Admin')->inRandomOrder()->first()->id, // Assign only non-admin staff
            'shift_start' => $shift_start,
            'shift_end' => $shift_end,
            'status' => $this->faker->randomElement(['Assigned', 'In Progress', 'Completed', 'Cancelled']),
        ];
    }
}