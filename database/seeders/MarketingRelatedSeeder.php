<?php

namespace Database\Seeders;

use App\Models\MarketingCampaign;
use App\Models\MarketingPlatform;
use App\Models\Staff;
use Illuminate\Database\Seeder;

class MarketingRelatedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure Staff exists for campaigns
        if (Staff::count() === 0) {
            Staff::factory()->count(3)->create(); // Limit staff to 3
        }

        // Create Marketing Platforms
        MarketingPlatform::factory()->count(3)->create();

        // Create Marketing Campaigns, which depend on platforms and staff
        MarketingCampaign::factory()->count(3)->create();
    }
}
