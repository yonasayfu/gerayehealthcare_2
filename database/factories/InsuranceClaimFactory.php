<?php

namespace Database\Factories;

use App\Models\InsuranceCompany;
use App\Models\InsurancePolicy;
use App\Models\Invoice;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InsuranceClaim>
 */
class InsuranceClaimFactory extends Factory
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
            'invoice_id' => Invoice::factory(),
            'insurance_company_id' => InsuranceCompany::factory(),
            'policy_id' => InsurancePolicy::factory(),
            'claim_status' => $this->faker->randomElement(['Submitted', 'Processed', 'Paid', 'Denied']),
            'coverage_amount' => $this->faker->randomFloat(2, 100, 1000),
            'paid_amount' => $this->faker->randomFloat(2, 0, 1000),
            'submitted_at' => $this->faker->dateTime,
            'processed_at' => $this->faker->dateTime,
            'payment_due_date' => $this->faker->date,
            'payment_received_at' => $this->faker->dateTime,
            'payment_method' => $this->faker->randomElement(['Cash', 'Bank Transfer']),
            'reimbursement_required' => $this->faker->boolean,
            'receipt_number' => $this->faker->word,
            'is_pre_authorized' => $this->faker->boolean,
            'pre_authorization_code' => $this->faker->word,
            'denial_reason' => $this->faker->sentence,
            'translated_notes' => $this->faker->sentence,
        ];
    }
}
