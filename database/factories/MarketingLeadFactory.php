<?php

namespace Database\Factories;

use App\Models\MarketingCampaign;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MarketingLead>
 */
class MarketingLeadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'source_campaign_id' => MarketingCampaign::all()->random()->id,
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'country' => $this->faker->country,
            'utm_source' => $this->faker->word,
            'utm_campaign' => $this->faker->word,
            'utm_medium' => $this->faker->word,
            'landing_page_id' => \App\Models\LandingPage::factory(),
            'lead_score' => $this->faker->numberBetween(1, 100),
            'status' => $this->faker->randomElement(['New', 'Contacted', 'Qualified', 'Disqualified', 'Converted']),
            'assigned_staff_id' => Staff::all()->random()->id,
            'converted_patient_id' => function (array $attributes) {
                if ($attributes['status'] === 'Converted') {
                    return \App\Models\Patient::updateOrCreate([
                        'email' => $attributes['email']], [
                        'full_name' => $attributes['first_name'] . ' ' . $attributes['last_name'],
                        'phone_number' => $attributes['phone'],
                        'patient_code' => 'PAT-' . str_pad(\App\Models\Patient::count() + 1, 5, '0', STR_PAD_LEFT),
                    ])->id;
                }
                return null;
            },
            'conversion_date' => function (array $attributes) {
                return $attributes['status'] === 'Converted' ? $this->faker->optional()->dateTimeBetween('-1 year', 'now') : null;
            },
            'notes' => $this->faker->paragraph,
        ];
    }
}
