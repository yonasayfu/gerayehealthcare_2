<?php

namespace Database\Factories;

use App\Models\Prescription;
use App\Models\PrescriptionItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\PrescriptionItem>
 */
class PrescriptionItemFactory extends Factory
{
    protected $model = PrescriptionItem::class;

    public function definition(): array
    {
        $medications = ['Amoxicillin', 'Ibuprofen', 'Paracetamol', 'Metformin', 'Atorvastatin', 'Omeprazole'];

        return [
            'prescription_id' => Prescription::inRandomOrder()->value('id') ?? Prescription::factory()->create()->id,
            'medication_name' => $this->faker->randomElement($medications),
            'dosage' => $this->faker->optional()->randomElement(['250mg', '500mg', '5ml', '10mg']),
            'frequency' => $this->faker->optional()->randomElement(['OD', 'BD', 'TID', 'QID', 'PRN']),
            'duration' => $this->faker->optional()->randomElement(['3 days', '5 days', '1 week', '2 weeks']),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
