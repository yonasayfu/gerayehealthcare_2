<?php

namespace Database\Factories;

use App\Models\Partner;
use App\Models\PartnerAgreement;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Referral>
 */
class ReferralFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'partner_id' => Partner::factory(),
            'agreement_id' => PartnerAgreement::inRandomOrder()->first()->id ?? null,
            'referred_patient_id' => Patient::factory()->create()->id,
            'referral_date' => $this->faker->date(),
            'status' => $this->faker->randomElement(['Pending', 'Converted', 'Rejected']),
            'notes' => $this->faker->sentence,
        ];
    }
}
