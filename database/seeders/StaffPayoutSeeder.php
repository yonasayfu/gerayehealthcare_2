<?php

namespace Database\Seeders;

use App\DTOs\CreateStaffPayoutDTO;
use App\Models\Patient;
use App\Models\Staff;
use App\Models\StaffPayout;
use App\Models\VisitService;
use App\Services\StaffPayoutService;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class StaffPayoutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(StaffPayoutService $payoutService): void
    {
        // Deterministic faker
        $faker = Faker::create();
        $faker->seed(20250815);

        // Ensure there are staff and patients, limiting to a small number for focused seeding
        if (Staff::count() === 0) {
            Staff::factory()->count(3)->create();
        }
        if (Patient::count() === 0) {
            Patient::factory()->count(5)->create();
        }

        $staff = Staff::all();
        $patientIds = Patient::pluck('id');

        foreach ($staff as $sIdx => $member) {
            // Create 2 completed visits per staff with predictable paid/unpaid pattern
            for ($i = 0; $i < 2; $i++) {
                $completedAt = Carbon::now()->copy()->subDays(7 + $i);
                $checkIn = $completedAt->copy()->setTime(9, 0);
                $checkOut = $completedAt->copy()->setTime(11 + ($i % 2), 0);

                // Pattern: first is unpaid, second is paid -> guarantees unpaid > 0 per staff
                $isPaid = $i === 1;

                VisitService::create([
                    'patient_id' => $patientIds->random(),
                    'staff_id' => $member->id,
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

        // Process payouts for ~half the staff to create realistic mix of paid vs waiting-for-payment
        $count = $staff->count();
        $half = (int) floor($count / 2);
        $toPayout = $staff->shuffle()->take(max(1, $half));
        foreach ($toPayout as $member) {
            try {
                $payoutService->processPayout(new CreateStaffPayoutDTO(
                    staff_id: $member->id,
                    notes: 'Automated seeder payout.'
                ));

                // Add an older payout for history/charts
                StaffPayout::create([
                    'staff_id' => $member->id,
                    'total_amount' => $faker->randomFloat(2, 150, 600),
                    'payout_date' => Carbon::now()->subDays(30),
                    'status' => 'Completed',
                    'notes' => 'Previous month seeded payout',
                ]);
            } catch (\Exception $e) {
                // ignore
            }
        }
    }
}
