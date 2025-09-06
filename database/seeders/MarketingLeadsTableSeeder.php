<?php

namespace Database\Seeders;

use App\Models\MarketingLead;
use Illuminate\Database\Seeder;

class MarketingLeadsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MarketingLead::factory()->count(3)->create();
    }
}
