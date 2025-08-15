<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Staff;
use App\Models\Patient;
use App\Models\VisitService;
use App\Services\StaffPayoutService;
use App\DTOs\CreateStaffPayoutDTO;
use Carbon\Carbon;
use Faker\Factory as Faker;

class StaffPayoutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(StaffPayoutService $payoutService): void
    {
        $faker = Faker::create();

        // Ensure there are staff and patients
        if (Staff::count() === 0) {
            Staff::factory()->count(10)->create();
        }
        if (Patient::count() === 0) {
            Patient::factory()->count(20)->create();
        }

        $staffIds = Staff::pluck('id');
        $patientIds = Patient::pluck('id');

        foreach ($staffIds as $staffId) {
            // Create a mix of paid and unpaid visits (bias toward leaving some unpaid)
            for ($i = 0; $i < rand(8, 18); $i++) {
                VisitService::create([
                    'patient_id' => $patientIds->random(),
                    'staff_id' => $staffId,
                    'scheduled_at' => Carbon::now()->subDays(rand(1, 60)),
                    'check_in_time' => Carbon::now()->subDays(rand(1, 60))->addHours(1),
                    'check_out_time' => Carbon::now()->subDays(rand(1, 60))->addHours(rand(2, 5)),
                    'visit_notes' => $faker->sentence,
                    'status' => 'Completed',
                    'cost' => $faker->randomFloat(2, 50, 250),
                    'is_paid_to_staff' => $faker->boolean(40), // 40% chance of being paid -> leave more unpaid
                ]);
            }
        }

        // Now, process payouts for a subset of staff who have unpaid visits (leave others unpaid for testing)
        $staffWithUnpaidVisits = Staff::whereHas('visitServices', function ($query) {
            $query->where('is_paid_to_staff', false)->where('status', 'Completed');
        })->get();

        // Randomly select ~50% of staff to receive payouts so others remain with unpaid balances for UI testing
        $toPayout = $staffWithUnpaidVisits->shuffle()->take((int) ceil($staffWithUnpaidVisits->count() / 2));

        foreach ($toPayout as $staff) {
            try {
                $payoutService->processPayout(new CreateStaffPayoutDTO(
                    staff_id: $staff->id,
                    notes: 'Automated seeder payout.'
                ));
            } catch (\Exception $e) {
                // Log or handle the exception if needed, e.g., when no unpaid visits are found
                // For this seeder, we can safely ignore it as the query ensures we have unpaid visits.
            }
        }
    }
}
