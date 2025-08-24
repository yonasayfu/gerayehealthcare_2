<?php

namespace Database\Factories;

use App\Models\MedicalVisit;
use App\Models\Patient;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\MedicalVisit>
 */
class MedicalVisitFactory extends Factory
{
    protected $model = MedicalVisit::class;

    public function definition(): array
    {
        $patientId = Patient::inRandomOrder()->value('id') ?? Patient::factory()->create()->id;
        $doctorId = Staff::inRandomOrder()->value('id') ?? Staff::factory()->create()->id;

        return [
            'patient_id' => $patientId,
            'visit_date' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'visit_type' => $this->faker->randomElement(['initial', 'follow_up', 'emergency']),
            'doctor_id' => $doctorId,
            'status' => $this->faker->randomElement(['scheduled', 'completed', 'cancelled']),
            'notes' => $this->faker->optional()->paragraph(),
        ];
    }
}
