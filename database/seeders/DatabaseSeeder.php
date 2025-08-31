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
        // Minimal seed for messaging demo: roles, staff, patients (<=7 total)
        $this->call([
            RolesAndPermissionsSeeder::class,
            StaffSeeder::class,
            PatientSeeder::class,
        ]);

        // --- THE FIX IS HERE ---
        // Use updateOrCreate to safely create or update the admin users.
        // This prevents duplicate email errors on subsequent seeding.

        // Create or find the Super Admin user
        $superAdminUser = User::updateOrCreate(
            ['email' => 'superadmin@geraye.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
            ]
        );
        $superAdminUser->assignRole(RoleEnum::SUPER_ADMIN->value);

        // Create or find the Admin user
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@geraye.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
            ]
        );
        $adminUser->assignRole(RoleEnum::ADMIN->value);
    }
}
