<?php

namespace Database\Factories;

use App\Models\InsurancePolicy;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmployeeInsuranceRecord>
 */
class EmployeeInsuranceRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'policy_id' => InsurancePolicy::factory(),
            'kebele_id' => $this->faker->word,
            'woreda' => $this->faker->word,
            'region' => $this->faker->word,
            'federal_id' => $this->faker->word,
            'ministry_department' => $this->faker->word,
            'employee_id_number' => $this->faker->word,
            'verified' => $this->faker->boolean,
            'verified_at' => $this->faker->dateTime,
        ];
    }
}
