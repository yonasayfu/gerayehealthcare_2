<?php

namespace Database\Seeders;

use App\Enums\RoleEnum; // Import the new Enum
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Roles using the Enum for safety and clarity
        Role::create(['name' => RoleEnum::SUPER_ADMIN]);
        Role::create(['name' => RoleEnum::ADMIN]);
        Role::create(['name' => RoleEnum::STAFF]);

        // Example of creating a permission
        // Permission::create(['name' => 'delete patients']);

        // Example of assigning a permission to a role
        // $superAdminRole = Role::findByName(RoleEnum::SUPER_ADMIN);
        // $superAdminRole->givePermissionTo('delete patients');
    }
}
