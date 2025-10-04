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
        $defaultPassword = env('TEST_USER_PASSWORD', 'password');
        $superAdminPassword = env('TEST_SUPERADMIN_PASSWORD', 'SuperAdmin123!');

        $this->call([
            DemoDataSeeder::class,
            MedicalVisitSeeder::class,
            InvoiceTestDataSeeder::class,
        ]);

        // --- LEGACY ADMIN USERS (Kept for backward compatibility) ---
        // Use updateOrCreate to safely create or update the admin users.
        // This prevents duplicate email errors on subsequent seeding.

        // Create or find the Legacy Super Admin user (if not created by TestUsersSeeder)
        $legacySuperAdminUser = User::updateOrCreate(
            ['email' => 'superadmin@geraye.com'],
            [
                'name' => 'Legacy Super Admin',
                'password' => bcrypt($superAdminPassword),
            ]
        );
        $legacySuperAdminUser->assignRole(RoleEnum::SUPER_ADMIN->value);

        // Create or find the Legacy Admin user (if not created by TestUsersSeeder)
        $legacyAdminUser = User::updateOrCreate(
            ['email' => 'admin@geraye.com'],
            [
                'name' => 'Legacy Admin User',
                'password' => bcrypt($defaultPassword),
            ]
        );
        $legacyAdminUser->assignRole(RoleEnum::ADMIN->value);

        // Seed 2 initial messages to demo notifications
        $staff = \App\Models\Staff::with('user')->first();
        $patient = \App\Models\Patient::with('user')->first();
        if ($staff && $staff->user && $patient && $patient->user) {
            \App\Models\Message::firstOrCreate([
                'sender_id' => $staff->user->id,
                'receiver_id' => $patient->user->id,
                'message' => 'Welcome to Geraye! Let us know how we can help.',
            ]);
            \App\Models\Message::firstOrCreate([
                'sender_id' => $patient->user->id,
                'receiver_id' => $staff->user->id,
                'message' => 'Thank you doctor. I will send my information shortly.',
            ]);
        }
    }
}
