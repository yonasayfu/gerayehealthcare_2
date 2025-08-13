<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Staff;
use App\Models\Patient;
use App\Models\VisitService;
use App\Models\StaffPayout;
use Carbon\Carbon;

class StaffPayoutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure there are staff and patients
        if (Staff::count() === 0) {
            Staff::factory()->count(5)->create();
        }
        if (Patient::count() === 0) {
            Patient::factory()->count(10)->create();
        }

        $staffIds = Staff::pluck('id');
        $patientIds = Patient::pluck('id');

        foreach ($staffIds as $staffId) {
            // Create some unpaid completed visit services for each staff
            for ($i = 0; $i < rand(3, 7); $i++) {
                $visitService = VisitService::create([
                    'patient_id' => $patientIds->random(),
                    'staff_id' => $staffId,
                    'scheduled_at' => Carbon::now()->subDays(rand(1, 30)),
                    'check_in_time' => Carbon::now()->subDays(rand(1, 30))->addHours(1),
                    'check_out_time' => Carbon::now()->subDays(rand(1, 30))->addHours(3),
                    'visit_notes' => 'Test visit notes for payout.',
                    'status' => 'Completed',
                    'cost' => rand(50, 200) * 100 / 100, // Random cost between 50 and 200
                    'is_paid_to_staff' => false,
                ]);
            }

            // Process a payout for this staff member
            $unpaidVisits = VisitService::where('staff_id', $staffId)
                ->where('is_paid_to_staff', false)
                ->where('status', 'Completed')
                ->get();

            if ($unpaidVisits->isNotEmpty()) {
                $totalAmount = $unpaidVisits->sum('cost');

                $payout = StaffPayout::create([
                    'staff_id' => $staffId,
                    'total_amount' => $totalAmount,
                    'payout_date' => Carbon::now(),
                    'status' => 'Completed',
                    'notes' => 'Automated test payout.',
                ]);

                // Attach visit services to payout and mark as paid
                $payout->visitServices()->attach($unpaidVisits->pluck('id'));
                VisitService::whereIn('id', $unpaidVisits->pluck('id'))->update(['is_paid_to_staff' => true]);
            }
        }
    }
}
