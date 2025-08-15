<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Staff;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Faker\Factory as Faker;

class LeaveRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        if (Staff::count() === 0) {
            Staff::factory()->count(10)->create();
        }

        $staffIds = Staff::pluck('id');

        foreach ($staffIds as $staffId) {
            // Create a few pending leave requests for each staff member
            for ($i = 0; $i < rand(1, 3); $i++) {
                $startDate = Carbon::now()->addDays(rand(10, 90));
                LeaveRequest::create([
                    'staff_id' => $staffId,
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $startDate->copy()->addDays(rand(1, 5))->toDateString(),
                    'reason' => $faker->sentence,
                    'status' => 'Pending',
                ]);
            }
        }

        // Admin perspective: Approve or deny some of the pending requests
        $pendingRequests = LeaveRequest::where('status', 'Pending')->get();

        foreach ($pendingRequests as $request) {
            if ($faker->boolean(75)) { // 75% chance to process the request
                $newStatus = $faker->randomElement(['Approved', 'Denied']);
                $request->update([
                    'status' => $newStatus,
                    'admin_notes' => $newStatus === 'Denied' ? $faker->paragraph : null,
                ]);
            }
        }
    }
}
