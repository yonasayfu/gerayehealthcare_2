<?php

namespace Database\Seeders;

use App\Models\ReferralDocument;
use Illuminate\Database\Seeder;

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
