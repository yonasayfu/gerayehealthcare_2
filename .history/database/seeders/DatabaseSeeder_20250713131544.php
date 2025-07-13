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
        // Call other seeders first to ensure roles and base data exist
        $this->call([
            RolesAndPermissionsSeeder::class,
            PatientSeeder::class,
            StaffSeeder::class,
            CaregiverAssignmentSeeder::class,
            ServiceSeeder::class, 
            LeaveRequestSeeder::class,// Assuming ServiceSeeder exists
        ]);

        // Create sample data using factories
        \App\Models\VisitService::factory(1)->create();
        \App\Models\Message::factory(15)->create();

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
