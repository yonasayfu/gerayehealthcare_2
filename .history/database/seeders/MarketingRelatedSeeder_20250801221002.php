<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MarketingPlatform;
use App\Models\MarketingCampaign;
use App\Models\Staff;

class MarketingRelatedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure Staff exists for campaigns
        if (Staff::count() === 0) {
            // You might want to run a StaffSeeder here or create some dummy staff
            // For now, let's create a minimal staff member if none exist
            Staff::factory()->create();
        }

        // Create Marketing Platforms
        MarketingPlatform::factory()->count(6)->create();

        // Create Marketing Campaigns, which depend on platforms and staff
        MarketingCampaign::factory()->count(6)->create();
    }
}