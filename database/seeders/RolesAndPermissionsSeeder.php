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
            'view_any_campaign_contents', 'view_campaign_contents', 'create_campaign_contents', 'update_campaign_contents', 'delete_campaign_contents', 'restore_campaign_contents', 'force_delete_campaign_contents',
            'view_any_marketing_tasks', 'view_marketing_tasks', 'create_marketing_tasks', 'update_marketing_tasks', 'delete_marketing_tasks', 'restore_marketing_tasks', 'force_delete_marketing_tasks',
            'view insurance companies', 'create insurance companies', 'update insurance companies', 'delete insurance companies',
            'view corporate clients', 'create corporate clients', 'update corporate clients', 'delete corporate clients',
            'view insurance policies', 'create insurance policies', 'update insurance policies', 'delete insurance policies',
            'view employee insurance records', 'create employee insurance records', 'update employee insurance records', 'delete employee insurance records',
            'view insurance claims', 'create insurance claims', 'update insurance claims', 'delete insurance claims',
            'view exchange rates', 'create exchange rates', 'update exchange rates', 'delete exchange rates',
            'view ethiopian calendar days', 'create ethiopian calendar days', 'update ethiopian calendar days', 'delete ethiopian calendar days',
            'view events', 'create events', 'edit events', 'delete events',
            'view eligibility criteria', 'create eligibility criteria', 'edit eligibility criteria', 'delete eligibility criteria',
            'view event recommendations', 'create event recommendations', 'edit event recommendations', 'delete event recommendations',
            'view event participants', 'create event participants', 'edit event participants', 'delete event participants',
            'view event staff assignments', 'create event staff assignments', 'edit event staff assignments', 'delete event staff assignments',
            'view event broadcasts', 'create event broadcasts', 'edit event broadcasts', 'delete event broadcasts',
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
            'view_any_campaign_contents', 'view_campaign_contents', 'create_campaign_contents', 'update_campaign_contents', 'delete_campaign_contents',
            'view_any_marketing_tasks', 'view_marketing_tasks', 'create_marketing_tasks', 'update_marketing_tasks', 'delete_marketing_tasks',
            'view insurance companies', 'create insurance companies', 'update insurance companies', 'delete insurance companies',
            'view corporate clients', 'create corporate clients', 'update corporate clients', 'delete corporate clients',
            'view insurance policies', 'create insurance policies', 'update insurance policies', 'delete insurance policies',
            'view employee insurance records', 'create employee insurance records', 'update employee insurance records', 'delete employee insurance records',
            'view insurance claims', 'create insurance claims', 'update insurance claims', 'delete insurance claims',
            'view exchange rates', 'create exchange rates', 'update exchange rates', 'delete exchange rates',
            'view ethiopian calendar days', 'create ethiopian calendar days', 'update ethiopian calendar days', 'delete ethiopian calendar days',
            'view events', 'create events', 'edit events', 'delete events',
            'view eligibility criteria', 'create eligibility criteria', 'edit eligibility criteria', 'delete eligibility criteria',
            'view event recommendations', 'create event recommendations', 'edit event recommendations', 'delete event recommendations',
            'view event participants', 'create event participants', 'edit event participants', 'delete event participants',
            'view event staff assignments', 'create event staff assignments', 'edit event staff assignments', 'delete event staff assignments',
            'view event broadcasts', 'create event broadcasts', 'edit event broadcasts', 'delete event broadcasts',
        ]);

        // Assign permissions to the 'Staff' role
        $staffRole->givePermissionTo([
            'view patients'
        ]);
    }
}
