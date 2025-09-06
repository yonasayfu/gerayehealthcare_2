<?php

namespace Database\Seeders;

use App\Models\LandingPage;
use App\Models\MarketingCampaign;
use Illuminate\Database\Seeder;

class LandingPageSeeder extends Seeder
{
    /**
     * Seed landing pages linked to campaigns.
     */
    public function run(): void
    {
        // Ensure there are campaigns to link to
        if (MarketingCampaign::count() === 0) {
            // Fallback: create a few campaigns if none exist
            MarketingCampaign::factory(3)->create();
        }

        $campaignIds = MarketingCampaign::query()->pluck('id')->all();
        if (empty($campaignIds)) {
            return; // nothing to seed against
        }

        // Create 10 landing pages distributed among campaigns
        foreach (range(1, 10) as $i) {
            $campaignId = $campaignIds[array_rand($campaignIds)];

            LandingPage::factory()->create([
                'campaign_id' => $campaignId,
                'language' => fake()->randomElement(['en', 'es', 'fr', 'de', 'zh']),
                'is_active' => (bool) random_int(0, 1),
                'views' => fake()->numberBetween(0, 1000),
                'submissions' => fake()->numberBetween(0, 500),
                'conversion_rate' => fake()->randomFloat(2, 0, 1),
                'template_used' => fake()->randomElement(['universal', 'lead-gen', 'promo', 'webinar']),
                'conversion_goal' => fake()->randomElement(['Lead', 'Signup', 'Download', 'Call']),
                'form_fields' => [
                    'name' => true,
                    'email' => true,
                    'phone' => fake()->boolean(50),
                ],
                'notes' => fake()->sentence(),
            ]);
        }
    }
}
