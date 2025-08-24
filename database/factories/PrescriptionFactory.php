<?php

namespace Database\Factories;

use App\Models\Prescription;
use App\Models\Patient;
use App\Models\Staff;
use App\Models\MedicalDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Prescription>
 */
class PrescriptionFactory extends Factory
{
    protected $model = Prescription::class;

    public function definition(): array
    {
        $patientId = Patient::inRandomOrder()->value('id') ?? Patient::factory()->create()->id;
        $creatorId = Staff::inRandomOrder()->value('id') ?? \App\Models\Staff::factory()->create()->id;
        $linkedDocId = MedicalDocument::inRandomOrder()->value('id');

        return [
            'patient_id' => $patientId,
            'medical_document_id' => $linkedDocId,
            'prescribed_date' => $this->faker->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
            'status' => $this->faker->randomElement(['active', 'completed', 'cancelled']),
            'instructions' => $this->faker->optional()->sentence(10),
            'created_by_staff_id' => $creatorId,
        ];
    }
}
