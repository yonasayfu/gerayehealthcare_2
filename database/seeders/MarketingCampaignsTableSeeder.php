<?php

namespace Database\Seeders;

use App\Models\MarketingCampaign;
use Illuminate\Database\Seeder;

class MarketingCampaignsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MarketingCampaign::factory(3)->create();
    }
}
