<?php

namespace Database\Seeders;

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
        // Create CEO Staff
        $ceoUser = User::where('email', 'ceo@geraye.com')->first();
        if ($ceoUser) {
            Staff::updateOrCreate(
                ['email' => $ceoUser->email],
                [
                    'first_name' => 'CEO',
                    'last_name' => 'User',
                    'user_id' => $ceoUser->id,
                    'position' => 'CEO',
                ]
            );
        }

        // Create COO Staff
        $cooUser = User::where('email', 'coo@geraye.com')->first();
        if ($cooUser) {
            Staff::updateOrCreate(
                ['email' => $cooUser->email],
                [
                    'first_name' => 'COO',
                    'last_name' => 'User',
                    'user_id' => $cooUser->id,
                    'position' => 'COO',
                ]
            );
        }

        // Create Admin Staff
        $adminUser = User::where('email', 'admin@geraye.com')->first();
        if ($adminUser) {
            Staff::updateOrCreate(
                ['email' => $adminUser->email],
                [
                    'first_name' => 'Admin',
                    'last_name' => 'User',
                    'user_id' => $adminUser->id,
                    'position' => 'Admin',
                ]
            );
        }

        // Create Staff
        $staffUser = User::where('email', 'staff@geraye.com')->first();
        if ($staffUser) {
            Staff::updateOrCreate(
                ['email' => $staffUser->email],
                [
                    'first_name' => 'Staff',
                    'last_name' => 'User',
                    'user_id' => $staffUser->id,
                    'position' => 'Nurse',
                ]
            );
        }
    }
}
