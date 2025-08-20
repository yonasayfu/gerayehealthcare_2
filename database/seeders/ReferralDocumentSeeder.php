<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReferralDocument;

class ReferralDocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Generate referral documents with related referral and staff via factory
        ReferralDocument::factory(20)->create();
    }
}
