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

        // ** STEP 1: Create Roles **
        Role::create(['name' => RoleEnum::SUPER_ADMIN->value]);
        $adminRole = Role::create(['name' => RoleEnum::ADMIN->value]);
        $staffRole = Role::create(['name' => RoleEnum::STAFF->value]);

        // ** STEP 2: Create Permissions **
        $permissions = [
            'view patients', 'create patients', 'edit patients', 'delete patients',
            'view staff', 'create staff', 'edit staff', 'delete staff',
            'view assignments', 'create assignments', 'edit assignments', 'delete assignments',
            'manage roles', 'manage users',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // ** STEP 3: Assign Permissions to Roles **
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