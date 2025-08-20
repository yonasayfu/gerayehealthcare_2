<?php

namespace Database\Factories;

use App\Models\Partner;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PartnerAgreement>
 */
class PartnerAgreementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'partner_id' => Partner::inRandomOrder()->first()->id ?? null,
            'agreement_title' => $this->faker->sentence,
            'agreement_type' => $this->faker->randomElement(['Referral Commission', 'Priority Service', 'Co-Marketing']),
            'status' => $this->faker->randomElement(['Draft', 'Active', 'Expired', 'Terminated']),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->optional()->date(),
            'priority_service_level' => $this->faker->optional()->randomElement(['Standard', 'Preferred', 'Premium']),
            'commission_type' => $this->faker->optional()->randomElement(['Percentage', 'FixedAmountPerPatient']),
            'commission_rate' => $this->faker->optional()->randomFloat(2, 5, 20),
            'terms_document_path' => $this->faker->optional()->url,
            'signed_by_staff_id' => Staff::inRandomOrder()->first()->id ?? null,
        ];
    }
}
