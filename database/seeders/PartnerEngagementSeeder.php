<?php

namespace Database\Seeders;

use App\Models\PartnerEngagement;
use Illuminate\Database\Seeder;

class PartnerEngagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PartnerEngagement::factory(10)->create();
    }
}
