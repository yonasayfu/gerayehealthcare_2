<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Seeder;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Staff::factory()->count(20)->create();
    }
}
