<?php

namespace Database\Factories;

use App\Models\MarketingCampaign;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CampaignMetric>
 */
class CampaignMetricFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Ensure reasonable relationships between metrics
        $impressions = $this->faker->numberBetween(1_000, 200_000);
        $clicks = (int) round($impressions * $this->faker->randomFloat(4, 0.005, 0.1)); // 0.5% - 10% CTR
        $conversions = (int) round($clicks * $this->faker->randomFloat(4, 0.02, 0.2)); // 2% - 20% CVR
        $cpc = $this->faker->randomFloat(4, 0.05, 1.5);
        $cpa = $this->faker->randomFloat(2, 1, 50);
        $revenue = $this->faker->randomFloat(2, $conversions * 10, $conversions * 200);
        $engagementRate = $this->faker->randomFloat(2, 0.5, 15.0);
        $reach = $impressions - $this->faker->numberBetween(0, (int) ($impressions * 0.3));

        return [
            'campaign_id' => MarketingCampaign::factory(),
            'date' => $this->faker->dateTimeBetween('-365 days', 'now'),
            'impressions' => $impressions,
            'clicks' => $clicks,
            'conversions' => $conversions,
            'cost_per_click' => $cpc,
            'cost_per_conversion' => $cpa,
            'roi_percentage' => $this->faker->randomFloat(2, -50, 300),
            'leads_generated' => $this->faker->numberBetween($conversions, $conversions + 200),
            'patients_acquired' => $this->faker->numberBetween((int) ($conversions * 0.3), $conversions),
            'revenue_generated' => $revenue,
            'engagement_rate' => $engagementRate,
            'reach' => $reach,
        ];
    }
}
