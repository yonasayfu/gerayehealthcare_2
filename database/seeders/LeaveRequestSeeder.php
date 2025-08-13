<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Staff;
use App\Models\LeaveRequest;
use Carbon\Carbon;

class LeaveRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure there are some staff members
        if (Staff::count() === 0) {
            Staff::factory()->count(5)->create();
        }

        $staffIds = Staff::pluck('id');

        foreach ($staffIds as $staffId) {
            // Create 3-5 random leave requests for each staff member
            for ($i = 0; $i < rand(3, 5); $i++) {
                $start = Carbon::now()->addDays(rand(1, 60));
                $end = $start->copy()->addDays(rand(1, 10));

                LeaveRequest::create([
                    'staff_id' => $staffId,
                    'start_date' => $start,
                    'end_date' => $end,
                    'reason' => $this->faker()->sentence(),
                    'status' => collect(['Pending', 'Approved', 'Denied'])->random(),
                    'admin_notes' => rand(0, 1) ? $this->faker()->paragraph() : null,
                ]);
            }
        }
    }

    protected function faker()
    {
        return \Faker\Factory::create();
    }
}
