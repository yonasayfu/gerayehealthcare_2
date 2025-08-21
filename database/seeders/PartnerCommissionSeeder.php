<?php

namespace Database\Seeders;

use App\Models\PartnerCommission;
use Illuminate\Database\Seeder;

class PartnerCommissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PartnerCommission::factory(10)->create();
    }
}
