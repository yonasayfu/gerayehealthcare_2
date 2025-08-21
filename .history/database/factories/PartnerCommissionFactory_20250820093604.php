<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\PartnerAgreement;
use App\Models\Referral;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PartnerCommission>
 */
class PartnerCommissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'agreement_id' => PartnerAgreement::inRandomOrder()->first()->id ?? null,
            'referral_id' => Referral::inRandomOrder()->first()->id ?? null,
            'invoice_id' => Invoice::inRandomOrder()->first()->id ?? null,
            'commission_amount' => $this->faker->randomFloat(2, 10, 1000),
            'calculation_date' => $this->faker->date(),
            'payout_date' => $this->faker->optional()->date(),
            'status' => $this->faker->randomElement(['Due', 'Paid', 'Voided']),
        ];
    }
}
