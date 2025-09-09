<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Create super admin user
        $superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        // Create admin user
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        // Create staff user
        $staff = User::factory()->create([
            'name' => 'Staff User',
            'email' => 'staff@example.com',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        // Create regular users
        User::factory(20)->create();

        // Create inactive users
        User::factory(5)->inactive()->create();

        // Assign roles if Spatie Permission is available
        if (class_exists(\Spatie\Permission\Models\Role::class)) {
            $superAdminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'super-admin']);
            $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
            $staffRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'staff']);
            $userRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'user']);

            $superAdmin->assignRole($superAdminRole);
            $admin->assignRole($adminRole);
            $staff->assignRole($staffRole);

            // Assign user role to regular users
            User::whereNotIn('email', ['superadmin@example.com', 'admin@example.com', 'staff@example.com'])
                ->where('is_active', true)
                ->limit(10)
                ->get()
                ->each(function ($user) use ($userRole) {
                    $user->assignRole($userRole);
                });
        }
    }
}
