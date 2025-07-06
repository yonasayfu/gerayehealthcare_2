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

        // Define Permissions for each module
        $permissions = [
            'view patients', 'create patients', 'edit patients', 'delete patients',
            'view staff', 'create staff', 'edit staff', 'delete staff',
            'view assignments', 'create assignments', 'edit assignments', 'delete assignments',
            'manage roles', // A special permission for the new module
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create Roles
        $superAdminRole = Role::create(['name' => RoleEnum::SUPER_ADMIN]);
        // A Super Admin gets all permissions automatically. We don't need to assign them.

        $adminRole = Role::create(['name' => RoleEnum::ADMIN]);
        $adminRole->givePermissionTo([
            'view patients', 'create patients', 'edit patients',
            'view staff', 'create staff', 'edit staff',
            'view assignments', 'create assignments', 'edit assignments',
        ]);

        $staffRole = Role::create(['name' => RoleEnum::STAFF]);
        // The staff role currently has no permissions to access these admin modules.
        // They only have access to their own pages via the 'staff' role middleware.
    }
}
