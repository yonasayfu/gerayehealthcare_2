<?php

namespace Database\Seeders;

use App\Models\CampaignContent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CampaignContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CampaignContent::factory()->count(5)->create();
    }
}
