<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MarketingPlatform;

class MarketingPlatformsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MarketingPlatform::factory()->create([
            'name' => 'TikTok',
            'api_endpoint' => 'https://ads.tiktok.com/api',
            'is_active' => true,
        ]);

        MarketingPlatform::factory()->create([
            'name' => 'Meta Ads',
            'api_endpoint' => 'https://graph.facebook.com/v18.0',
            'is_active' => true,
        ]);

        MarketingPlatform::factory()->create([
            'name' => 'Google Ads',
            'api_endpoint' => 'https://googleads.googleapis.com/v15',
            'is_active' => true,
        ]);

        MarketingPlatform::factory()->create([
            'name' => 'LinkedIn Ads',
            'api_endpoint' => 'https://api.linkedin.com/v2',
            'is_active' => true,
        ]);

        MarketingPlatform::factory(5)->create(); // Create 5 more random platforms
    }
}
