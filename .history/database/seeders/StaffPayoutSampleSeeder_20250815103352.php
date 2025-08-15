<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Staff;
use App\Models\Patient;
use App\Models\VisitService;
use App\Models\StaffPayout;
use App\DTOs\CreateStaffPayoutDTO;
use App\Services\StaffPayoutService as AdminStaffPayoutService;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class StaffPayoutSampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(AdminStaffPayoutService $payoutService): void
    {
        // Deterministic faker for repeatable sample data
        $faker = Faker::create();
        $faker->seed(20250815);

        // Ensure base records
        if (Staff::count() === 0) {
            \App\Models\Staff::factory()->count(8)->create();
        }
        if (Patient::count() === 0) {
            \App\Models\Patient::factory()->count(24)->create();
        }

        $staffMembers = Staff::all();
        $patientIds = Patient::pluck('id');

        // Wipe existing visit services/payouts if needed? We avoid destructive ops here.
        // Instead, we add a consistent set on top so that UI has non-zero values.

        foreach ($staffMembers as $index => $staff) {
            // Create 10 completed visits per staff with a predictable paid/unpaid pattern
            for ($i = 0; $i < 10; $i++) {
                $completedAt = Carbon::now()->copy()->subDays(5 + $i);
                $checkIn = $completedAt->copy()->setTime(9, 0);
                $checkOut = $completedAt->copy()->setTime(10 + ($i % 3), 30);

                // Pattern: every 3rd visit is paid, others unpaid -> guarantees unpaid > 0
                $isPaid = ($i % 3) === 0;

                VisitService::create([
                    'patient_id' => $patientIds->random(),
                    'staff_id' => $staff->id,
                    'scheduled_at' => $completedAt->copy()->subHours(2),
                    'check_in_time' => $checkIn,
                    'check_out_time' => $checkOut,
                    'visit_notes' => $faker->sentence,
                    'status' => 'Completed',
                    'cost' => $faker->randomFloat(2, 80, 200),
                    'is_paid_to_staff' => $isPaid,
                ]);
            }
        }

        // Process payouts for ~50% of staff to create some payout history but leave others with unpaid totals
        $staffToPayout = $staffMembers->shuffle()->take((int) ceil($staffMembers->count() / 2));
        foreach ($staffToPayout as $staff) {
            try {
                $payoutService->processPayout(new CreateStaffPayoutDTO(
                    staff_id: $staff->id,
                    notes: 'Sample seeded payout.'
                ));

                // Add an older payout to enrich chart/history
                StaffPayout::create([
                    'staff_id' => $staff->id,
                    'total_amount' => $faker->randomFloat(2, 150, 600),
                    'payout_date' => Carbon::now()->subDays(30),
                    'status' => 'Completed',
                    'notes' => 'Previous month seeded payout',
                ]);
            } catch (\Exception $e) {
                // continue
            }
        }
    }
}
