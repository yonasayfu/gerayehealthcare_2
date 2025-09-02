<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $roles = [
            'super-admin', // Has all permissions
            'admin', // Can manage users and staff
            'coo', // Can manage staff and reports
            'ceo', // Can view all reports and analytics
            'staff', // Can manage their own profile and assigned tasks
            'guest', // Limited access, can only view public content
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Create permissions
        $permissions = [
            // User management
            'view-users',
            'create-users',
            'update-users',
            'delete-users',

            // Staff management
            'view-staff',
            'create-staff',
            'update-staff',
            'delete-staff',

            // Role management
            'assign-roles',
            'view-roles',

            // Profile management
            'view-profile',
            'update-profile',
            'change-password',

            // Reports and analytics
            'view-reports',
            'export-reports',

            // System management
            'manage-settings',
            'view-logs',

            // Public access
            'view-public-content',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign all permissions to super-admin
        $superAdmin = Role::findByName('super-admin');
        $superAdmin->givePermissionTo(Permission::all());

        // Assign permissions to admin
        $admin = Role::findByName('admin');
        $admin->givePermissionTo([
            'view-users', 'create-users', 'update-users', 'delete-users',
            'view-staff', 'create-staff', 'update-staff', 'delete-staff',
            'view-profile', 'update-profile', 'change-password',
            'view-reports', // Add this permission
            'view-public-content',
        ]);

        // Assign permissions to COO
        $coo = Role::findByName('coo');
        $coo->givePermissionTo([
            'view-staff', 'update-staff',
            'view-reports', 'export-reports',
            'view-profile', 'update-profile', 'change-password',
            'view-public-content',
        ]);

        // Assign permissions to CEO
        $ceo = Role::findByName('ceo');
        $ceo->givePermissionTo([
            'view-reports', 'export-reports',
            'view-profile', 'update-profile', 'change-password',
            'view-public-content',
        ]);

        // Assign permissions to staff
        $staff = Role::findByName('staff');
        $staff->givePermissionTo([
            'view-profile', 'update-profile', 'change-password',
            'view-public-content',
        ]);

        // Assign permissions to guest
        $guest = Role::findByName('guest');
        $guest->givePermissionTo([
            'view-public-content',
        ]);
    }
}
