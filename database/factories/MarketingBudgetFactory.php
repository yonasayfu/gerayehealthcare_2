<?php

namespace Database\Factories;

use App\Models\MarketingCampaign;
use App\Models\MarketingPlatform;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Eloquent\Factories\Factory<\App\Models\MarketingBudget>
 */
class MarketingBudgetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-6 months', 'now');
        $endDate = $this->faker->dateTimeBetween($startDate, '+6 months');
        $allocatedAmount = $this->faker->randomFloat(2, 1000, 100000);
        $spentAmount = $this->faker->randomFloat(2, 0, $allocatedAmount);

        return [
            'campaign_id' => MarketingCampaign::inRandomOrder()->first()->id ?? MarketingCampaign::factory()->create()->id,
            'platform_id' => MarketingPlatform::inRandomOrder()->first()->id ?? MarketingPlatform::factory()->create()->id,
            'budget_name' => $this->faker->sentence(3).' Budget',
            'description' => $this->faker->paragraph(),
            'allocated_amount' => $allocatedAmount,
            'spent_amount' => $spentAmount,
            'period_start' => $startDate,
            'period_end' => $endDate,
            'status' => $this->faker->randomElement(['Planned', 'Active', 'Completed', 'On Hold', 'Cancelled']),
        ];
    }
}
