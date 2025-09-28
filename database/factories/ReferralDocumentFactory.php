<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReferralDocument>
 */
class ReferralDocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'referral_id' => \App\Models\Referral::factory(),
            'uploaded_by_staff_id' => \App\Models\Staff::factory(),
            'document_name' => $this->faker->word.'.pdf',
            'document_path' => 'referral_documents/'.$this->faker->uuid.'.pdf',
            'document_type' => $this->faker->randomElement(['Clinical Summary', 'Prescription', 'Lab Result', 'Imaging Report']),
            'status' => $this->faker->randomElement(['Uploaded', 'Sent', 'Received', 'Reviewed']),
        ];
    }
}
