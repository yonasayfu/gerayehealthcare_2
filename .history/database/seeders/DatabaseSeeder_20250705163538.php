<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class, // This runs first
            PatientSeeder::class,
            StaffSeeder::class,
            CaregiverAssignmentSeeder::class,
        ]);

        // Create the Super Admin User
        $superAdminUser = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@geraye.com',
            'password' => bcrypt('password'), // Default password is 'password'
        ]);
        $superAdminUser->assignRole(RoleEnum::SUPER_ADMIN);

        // Create a regular Admin User (for the secretary role)
        $adminUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@geraye.com',
            'password' => bcrypt('password'), // Default password is 'password'
        ]);
        $adminUser->assignRole(RoleEnum::ADMIN);
    }
}
