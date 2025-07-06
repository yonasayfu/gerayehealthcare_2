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
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Define Permissions for all modules
        $permissions = [
            'view patients', 'create patients', 'edit patients', 'delete patients',
            'view staff', 'create staff', 'edit staff', 'delete staff',
            'view assignments', 'create assignments', 'edit assignments', 'delete assignments',
            'manage roles', 'manage users',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Super Admin gets all permissions automatically via a Gate.

        $adminRole = Role::findByName(RoleEnum::ADMIN);
        $adminRole->givePermissionTo([
            'view patients', 'create patients', 'edit patients',
            'view staff', 'create staff', 'edit staff',
            'view assignments', 'create assignments', 'edit assignments',
        ]);

        $staffRole = Role::findByName(RoleEnum::STAFF);
        // Give staff permission to view patients as an example
        $staffRole->givePermissionTo('view patients');
    }
}
