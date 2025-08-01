<?php

namespace Database\Seeders;

use App\Models\MarketingTask;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MarketingTaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MarketingTask::factory()->count(5)->create();
    }
}
