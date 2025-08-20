<?php

namespace Database\Seeders;

use App\Models\PartnerAgreement;
use Illuminate\Database\Seeder;

class PartnerAgreementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PartnerAgreement::factory(10)->create();
    }
}
