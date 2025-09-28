<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MarketingModuleMainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            MarketingPlatformsTableSeeder::class,
            LeadSourcesTableSeeder::class,
            MarketingCampaignsTableSeeder::class,
            MarketingLeadsTableSeeder::class,
            MarketingBudgetsTableSeeder::class,
            CampaignContentsTableSeeder::class,
            MarketingTasksTableSeeder::class,
        ]);
    }
}
