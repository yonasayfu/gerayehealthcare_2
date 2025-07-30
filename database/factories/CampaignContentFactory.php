<?php

namespace Database\Factories;

use App\Models\MarketingCampaign;
use App\Models\MarketingPlatform;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CampaignContent>
 */
class CampaignContentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'campaign_id' => MarketingCampaign::factory(),
            'platform_id' => MarketingPlatform::factory(),
            'content_type' => $this->faker->randomElement(['text', 'image', 'video']),
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'media_url' => $this->faker->imageUrl(),
            'scheduled_post_date' => $this->faker->dateTimeBetween('+1 week', '+1 month'),
            'actual_post_date' => null,
            'status' => $this->faker->randomElement(['draft', 'scheduled', 'posted', 'failed']),
            'engagement_metrics' => [
                'likes' => $this->faker->numberBetween(0, 1000),
                'comments' => $this->faker->numberBetween(0, 500),
                'shares' => $this->faker->numberBetween(0, 200),
            ],
        ];
    }
}
