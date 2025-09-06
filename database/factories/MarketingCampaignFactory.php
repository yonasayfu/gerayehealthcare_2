<?php

namespace Database\Factories;

use App\Models\MarketingPlatform;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MarketingCampaign>
 */
class MarketingCampaignFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-1 year', 'now');
        $endDate = $this->faker->dateTimeBetween($startDate, '+1 year');
        $budgetAllocated = $this->faker->randomFloat(2, 100, 10000);
        $budgetSpent = $this->faker->randomFloat(2, 50, $budgetAllocated);

        return [
            'platform_id' => MarketingPlatform::all()->random()->id,
            'campaign_name' => $this->faker->sentence(3),
            'campaign_type' => $this->faker->randomElement(['Awareness', 'Lead Gen', 'Conversion', 'Engagement']),
            'target_audience' => json_encode(['age_group' => '25-45', 'gender' => 'female', 'interests' => ['health', 'wellness']]),
            'budget_allocated' => $budgetAllocated,
            'budget_spent' => $budgetSpent,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $this->faker->randomElement(['Draft', 'Active', 'Paused', 'Completed']),
            'urgency' => $this->faker->randomElement(['Low', 'Medium', 'High', 'Critical']),
            'utm_campaign' => $this->faker->slug(),
            'utm_source' => $this->faker->word(),
            'utm_medium' => $this->faker->word(),
            'assigned_staff_id' => Staff::all()->random()->id,
            'responsible_staff_id' => Staff::all()->random()->id,
            'created_by_staff_id' => Staff::all()->random()->id,
            'goals' => json_encode(['leads' => $this->faker->numberBetween(100, 1000), 'conversions' => $this->faker->numberBetween(10, 200)]),
        ];
    }
}
