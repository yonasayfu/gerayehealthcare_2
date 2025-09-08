<?php

namespace Database\Seeders;

use App\Models\EligibilityCriteria;
use Illuminate\Database\Seeder;

class EligibilityCriteriaSeeder extends Seeder
{
    public function run(): void
    {
        EligibilityCriteria::factory()->count(10)->create();
    }
}
