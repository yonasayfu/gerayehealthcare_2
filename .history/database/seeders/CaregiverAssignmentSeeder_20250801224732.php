<?php

namespace Database\Seeders;

use App\Models\CaregiverAssignment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CaregiverAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CaregiverAssignment::factory(6)->create();
    }
}