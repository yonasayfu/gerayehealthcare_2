<?php

namespace Database\Seeders;

use App\Models\Partner;
use App\Models\PartnerAgreement;
use App\Models\PartnerCommission;
use App\Models\PartnerEngagement;
use App\Models\Patient;
use App\Models\Referral;
use App\Models\ReferralDocument;
use App\Models\SharedInvoice;
use App\Models\Staff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PartnershipModuleSeeder extends Seeder
{
    /**
     * Seed partners, agreements, commissions, engagements, and referrals.
     */
    public function run(): void
    {
        $staff = Staff::orderBy('id')->take(3)->get();
        if ($staff->isEmpty()) {
            $staff = Staff::factory()->count(3)->create();
        }

        $patients = Patient::orderBy('id')->take(5)->get();
        if ($patients->isEmpty()) {
            $patients = Patient::factory()->count(5)->create();
        }

        $partners = Partner::factory()->count(5)->create();

        foreach ($partners as $index => $partner) {
            $agreement = PartnerAgreement::factory()->create([
                'partner_id' => $partner->id,
                'start_date' => Carbon::now()->subMonths($index + 1),
                'end_date' => Carbon::now()->addMonths(6 + $index),
                'status' => 'Active',
            ]);

            PartnerCommission::factory()->create([
                'agreement_id' => $agreement->id,
                'commission_amount' => 100 + ($index * 10),
                'status' => 'Due',
            ]);

            PartnerEngagement::factory()->create([
                'partner_id' => $partner->id,
                'engagement_type' => 'Referral Program',
            ]);

            $patient = $patients[$index % $patients->count()];
            $referral = Referral::factory()->create([
                'partner_id' => $partner->id,
                'referred_patient_id' => $patient->id,
                'status' => $index % 2 === 0 ? 'Converted' : 'Pending',
                'referral_date' => Carbon::now()->subDays($index * 3),
            ]);

            ReferralDocument::factory()->create([
                'referral_id' => $referral->id,
                'uploaded_by_staff_id' => $staff[$index % $staff->count()]->id,
            ]);

            $invoice = \App\Models\Invoice::factory()->create([
                'patient_id' => $patient->id,
                'grand_total' => 1500 + ($index * 200),
                'status' => $index % 2 === 0 ? 'Paid' : 'Pending',
            ]);

            SharedInvoice::factory()->create([
                'invoice_id' => $invoice->id,
                'partner_id' => $partner->id,
                'status' => $index % 2 === 0 ? 'Shared' : 'Acknowledged',
            ]);
        }
    }
}
