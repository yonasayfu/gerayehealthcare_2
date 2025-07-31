<?php

namespace Database\Factories;

use App\Models\CorporateClient;
use App\Models\InsuranceCompany;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InsurancePolicy>
 */
class InsurancePolicyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'insurance_company_id' => InsuranceCompany::factory(),
            'corporate_client_id' => CorporateClient::factory(),
            'service_type' => $this->faker->word,
            'service_type_amharic' => $this->faker->word,
            'coverage_percentage' => $this->faker->randomFloat(2, 0, 100),
            'coverage_type' => $this->faker->randomElement(['Full', 'Partial']),
            'is_active' => $this->faker->boolean,
            'notes' => $this->faker->sentence,
        ];
    }
}
