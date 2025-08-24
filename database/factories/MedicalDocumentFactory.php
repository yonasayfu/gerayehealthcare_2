<?php

namespace Database\Factories;

use App\Models\MedicalDocument;
use App\Models\Patient;
use App\Models\Staff;
use App\Models\MedicalVisit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\MedicalDocument>
 */
class MedicalDocumentFactory extends Factory
{
    protected $model = MedicalDocument::class;

    public function definition(): array
    {
        $patientId = Patient::inRandomOrder()->value('id') ?? Patient::factory()->create()->id;
        $visitId = MedicalVisit::inRandomOrder()->value('id');
        $creatorId = Staff::inRandomOrder()->value('id') ?? \App\Models\Staff::factory()->create()->id;

        $docType = $this->faker->randomElement(['doctor_note', 'lab_request', 'lab_result', 'prescription', 'other']);

        return [
            'patient_id' => $patientId,
            'medical_visit_id' => $visitId,
            'document_type' => $docType,
            'title' => ucfirst($docType) . ' - ' . $this->faker->sentence(3),
            'document_date' => $this->faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'file_path' => $this->faker->optional(0.6)->filePath(),
            'summary' => $this->faker->optional()->paragraph(),
            'is_printed' => $this->faker->boolean(30),
            'created_by_staff_id' => $creatorId,
        ];
    }
}
