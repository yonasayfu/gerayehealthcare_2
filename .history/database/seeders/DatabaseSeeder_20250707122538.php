<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            PatientSeeder::class,
            StaffSeeder::class,
            CaregiverAssignmentSeeder::class,
        ]);
        \App\Models\VisitService::factory(50)->create();
        $superAdminUser = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@geraye.com',
            'password' => bcrypt('password'),
        ]);
        //$superAdminUser->assignRole(RoleEnum::SUPER_ADMIN);
        $superAdminUser->assignRole(RoleEnum::SUPER_ADMIN->value);

        $adminUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@geraye.com',
            'password' => bcrypt('password'),
        ]);
        $adminUser->assignRole(RoleEnum::ADMIN);
        $adminUser->assignRole(RoleEnum::ADMIN->value);
    }
}
