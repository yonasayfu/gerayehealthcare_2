<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // --- Create Permissions ---
        // Using updateOrCreate to prevent errors on re-seeding
        $permissions = [
            'view patients', 'create patients', 'edit patients', 'delete patients',
            'view staff', 'create staff', 'edit staff', 'delete staff',
            'view assignments', 'create assignments', 'edit assignments', 'delete assignments',
            'manage roles', 'manage users',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // --- Create Roles ---
        // Using firstOrCreate to prevent errors on re-seeding
        $superAdminRole = Role::firstOrCreate(['name' => RoleEnum::SUPER_ADMIN->value]);
        $adminRole = Role::firstOrCreate(['name' => RoleEnum::ADMIN->value]);
        $staffRole = Role::firstOrCreate(['name' => RoleEnum::STAFF->value]);

        // --- Assign Permissions to Roles ---
        // Super Admin gets all permissions automatically via a Gate in AuthServiceProvider.

        // Assign permissions to the 'Admin' role
        $adminRole->givePermissionTo([
            'view patients', 'create patients', 'edit patients', 'delete patients',
            'view staff', 'create staff', 'edit staff', 'delete staff',
            'view assignments', 'create assignments', 'edit assignments', 'delete assignments',
        ]);

        // Assign permissions to the 'Staff' role
        $staffRole->givePermissionTo([
            'view patients'
        ]);
    }
}
