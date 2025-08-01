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
        // Create 20 staff members using the factory
        Staff::factory()->count(6)->make()->each(function ($staff) {
            $user = User::updateOrCreate(
                ['email' => $staff->email],
                [
                    'name' => $staff->first_name . ' ' . $staff->last_name,
                    'password' => bcrypt('password'),
                ]
            );

            $user->assignRole(RoleEnum::STAFF);

            Staff::updateOrCreate(
                ['email' => $staff->email],
                [
                    'first_name' => $staff->first_name,
                    'last_name' => $staff->last_name,
                    'phone' => $staff->phone,
                    'position' => $staff->position,
                    'department' => $staff->department,
                    'role' => $staff->role,
                    'status' => $staff->status,
                    'hire_date' => $staff->hire_date,
                    'photo' => $staff->photo,
                    'user_id' => $user->id,
                ]
            );
        });
    }
}