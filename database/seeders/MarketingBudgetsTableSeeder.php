<?php

namespace Database\Seeders;

use App\Models\MarketingBudget;
use Illuminate\Database\Seeder;

class MarketingBudgetsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MarketingBudget::factory()->count(3)->create();
    }
}
