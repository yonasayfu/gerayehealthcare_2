<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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

        // Create Roles
        Role::create(['name' => 'Super Admin']);
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Staff']);

        // Later, when we need specific permissions, we can define them here.
        // For example, a permission to delete patients:
        // Permission::create(['name' => 'delete patients']);
        
        // Then we could assign that permission only to the Super Admin role:
        // $superAdminRole = Role::findByName('Super Admin');
        // $superAdminRole->givePermissionTo('delete patients');
    }
}
