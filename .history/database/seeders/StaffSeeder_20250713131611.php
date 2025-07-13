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
        Staff::factory()->count(15)->create()->each(function ($staff) {
            
            // For each staff member, create a corresponding user account.
            // This prevents issues if an email from the factory already exists in the users table.
            $user = User::factory()->create([
                'name' => $staff->first_name . ' ' . $staff->last_name,
                'email' => $staff->email,
                'password' => bcrypt('password'), // All staff get 'password' as their default password
            ]);

            // Assign the 'Staff' role to the new user
            $user->assignRole(RoleEnum::STAFF);

            // Link the user account to the staff profile by updating the user_id
            $staff->user_id = $user->id;
            $staff->save();
        });
    }
}