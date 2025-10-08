<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultPassword = env('TEST_USER_PASSWORD', 'password');
        $superAdminPassword = env('TEST_SUPERADMIN_PASSWORD', 'SuperAdmin123!');

        // Create Super Admin
        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@geraye.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt($superAdminPassword),
            ]
        );
        $superAdmin->assignRole(RoleEnum::SUPER_ADMIN->value);

        // Create CEO
        $ceo = User::updateOrCreate(
            ['email' => 'ceo@geraye.com'],
            [
                'name' => 'CEO',
                'password' => bcrypt($defaultPassword),
            ]
        );
        $ceo->assignRole(RoleEnum::CEO->value);

        // Create COO
        $coo = User::updateOrCreate(
            ['email' => 'coo@geraye.com'],
            [
                'name' => 'COO',
                'password' => bcrypt($defaultPassword),
            ]
        );
        $coo->assignRole(RoleEnum::COO->value);

        // Create Admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@geraye.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt($defaultPassword),
            ]
        );
        $admin->assignRole(RoleEnum::ADMIN->value);

        // Create Staff
        $staff = User::updateOrCreate(
            ['email' => 'staff@geraye.com'],
            [
                'name' => 'Staff',
                'password' => bcrypt($defaultPassword),
            ]
        );
        $staff->assignRole(RoleEnum::STAFF->value);

        // Create Patient
        $patient = User::updateOrCreate(
            ['email' => 'patient@geraye.com'],
            [
                'name' => 'Patient',
                'password' => bcrypt($defaultPassword),
            ]
        );
    }
}
