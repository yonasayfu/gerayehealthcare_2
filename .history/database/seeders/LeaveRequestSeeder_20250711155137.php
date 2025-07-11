<?php

namespace Database\Seeders;

use App\Models\LeaveRequest;
use Illuminate\Database\Seeder;

class LeaveRequestSeeder extends Seeder
{
    public function run(): void
    {
        // Create 25 leave requests for various staff members
        LeaveRequest::factory()->count(25)->create();
    }
}