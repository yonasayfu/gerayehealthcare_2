<?php

namespace Database\Seeders;

use App\Models\MarketingPlatform;
use Illuminate\Database\Seeder;

class MarketingPlatformsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MarketingPlatform::factory(3)->create();
    }
}
