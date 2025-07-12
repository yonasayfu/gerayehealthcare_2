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
            // --- THIS IS THE FIX ---
            // Instead of finding a random record, this tells the factory to
            // create a new Patient and a new Staff member for this assignment.
            'patient_id' => Patient::factory(),
            'staff_id' => Staff::factory()->state(['role' => 'Nurse']), // Creates a non-admin staff member

            // --- Your existing logic remains the same ---
            'shift_start' => $shift_start,
            'shift_end' => $shift_end,
            'status' => $this->faker->randomElement(['Assigned', 'In Progress', 'Completed', 'Cancelled']),
        ];
    }
}