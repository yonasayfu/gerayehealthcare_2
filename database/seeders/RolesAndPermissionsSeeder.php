<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // --- Create Comprehensive Permissions ---
        // Using updateOrCreate to prevent errors on re-seeding
        $permissions = [
            // Patient Management
            'view patients', 'create patients', 'edit patients', 'delete patients', 'export patients',

            // Staff Management
            'view staff', 'create staff', 'edit staff', 'delete staff', 'export staff',
            'view staff schedules', 'manage staff schedules', 'approve leave requests',

            // User & Role Management
            'view users', 'create users', 'edit users', 'delete users', 'export users',
            'view roles', 'create roles', 'edit roles', 'delete roles',
            'assign roles', 'manage permissions', 'manage roles', 'manage users',

            // Assignment Management
            'view assignments', 'create assignments', 'edit assignments', 'delete assignments',

            // Marketing & Campaign Management
            'view_any_campaign_contents', 'view_campaign_contents', 'create_campaign_contents', 'update_campaign_contents', 'delete_campaign_contents', 'restore_campaign_contents', 'force_delete_campaign_contents',
            'view_any_marketing_tasks', 'view_marketing_tasks', 'create_marketing_tasks', 'update_marketing_tasks', 'delete_marketing_tasks', 'restore_marketing_tasks', 'force_delete_marketing_tasks',
            'view marketing campaigns', 'create marketing campaigns', 'edit marketing campaigns', 'delete marketing campaigns',
            'view marketing leads', 'create marketing leads', 'edit marketing leads', 'delete marketing leads',
            'view marketing budgets', 'create marketing budgets', 'edit marketing budgets', 'delete marketing budgets',
            'view marketing analytics', 'export marketing reports',

            // Insurance Management
            'view insurance companies', 'create insurance companies', 'update insurance companies', 'delete insurance companies',
            'view corporate clients', 'create corporate clients', 'update corporate clients', 'delete corporate clients',
            'view insurance policies', 'create insurance policies', 'update insurance policies', 'delete insurance policies',
            'view employee insurance records', 'create employee insurance records', 'update employee insurance records', 'delete employee insurance records',
            'view insurance claims', 'create insurance claims', 'update insurance claims', 'delete insurance claims',

            // Inventory Management
            'view inventory items', 'create inventory items', 'update inventory items', 'delete inventory items',
            'view inventory requests', 'create inventory requests', 'update inventory requests', 'delete inventory requests',
            'view inventory transactions', 'create inventory transactions', 'update inventory transactions', 'delete inventory transactions',
            'view inventory alerts', 'create inventory alerts', 'update inventory alerts', 'delete inventory alerts',
            'view inventory maintenance records', 'create inventory maintenance records', 'update inventory maintenance records', 'delete inventory maintenance records',
            'export inventory reports', 'manage inventory suppliers',

            // Financial Management (Comprehensive)
            'view invoices', 'create invoices', 'edit invoices', 'delete invoices', 'export invoices',
            'view financial reports', 'export financial reports', 'manage budgets',
            'view staff payouts', 'create staff payouts', 'edit staff payouts', 'delete staff payouts', 'export staff payouts',
            'view financial analytics', 'view revenue reports', 'view expense reports',
            'manage financial settings', 'approve financial transactions', 'view audit logs',
            'manage payment methods', 'view cash flow', 'manage financial accounts',
            'view exchange rates', 'create exchange rates', 'update exchange rates', 'delete exchange rates',

            // Calendar & Events
            'view ethiopian calendar days', 'create ethiopian calendar days', 'update ethiopian calendar days', 'delete ethiopian calendar days',
            'view events', 'create events', 'edit events', 'delete events',
            'view eligibility criteria', 'create eligibility criteria', 'edit eligibility criteria', 'delete eligibility criteria',
            'view event recommendations', 'create event recommendations', 'edit event recommendations', 'delete event recommendations',
            'view event participants', 'create event participants', 'edit event participants', 'delete event participants',
            'view event staff assignments', 'create event staff assignments', 'edit event staff assignments', 'delete event staff assignments',
            'view event broadcasts', 'create event broadcasts', 'edit event broadcasts', 'delete event broadcasts',

            // Analytics & Reporting
            'view analytics dashboard', 'view system reports', 'export system reports',
            'view performance metrics', 'view user analytics', 'view financial analytics',
            'view executive dashboard', 'view operational reports',

            // System Administration
            'view system settings', 'edit system settings', 'manage system backups',
            'view system logs', 'manage system maintenance', 'view system health',

            // Communication & Messaging
            'view messages', 'send messages', 'delete messages', 'manage group messages', 'create groups', 'export messages',
            'view notifications', 'send notifications', 'manage notification settings',

            // Profile & Personal
            'view own profile', 'edit own profile', 'change own password',
            'view public content', 'access guest features',

            // Medical & Clinical (Healthcare specific)
            'view medical records', 'create medical records', 'edit medical records',
            'view prescriptions', 'create prescriptions', 'edit prescriptions',
            'view appointments', 'create appointments', 'edit appointments', 'cancel appointments',
            'view visit services', 'create visit services', 'edit visit services',
            'view medical documents', 'upload medical documents', 'download medical documents',
            'view lab results', 'create lab results', 'edit lab results', 'delete lab results',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // --- Create All Roles ---
        // Using firstOrCreate to prevent errors on re-seeding
        $superAdminRole = Role::firstOrCreate(['name' => RoleEnum::SUPER_ADMIN->value]);
        $ceoRole = Role::firstOrCreate(['name' => RoleEnum::CEO->value]);
        $cooRole = Role::firstOrCreate(['name' => RoleEnum::COO->value]);
        $adminRole = Role::firstOrCreate(['name' => RoleEnum::ADMIN->value]);
        $staffRole = Role::firstOrCreate(['name' => RoleEnum::STAFF->value]);
        $guestRole = Role::firstOrCreate(['name' => RoleEnum::GUEST->value]);

        // --- Assign Permissions to Roles ---
        // Super Admin gets all permissions automatically via a Gate in AuthServiceProvider.
        $superAdminRole->givePermissionTo(Permission::all());

        // CEO - Executive level access with strategic oversight
        $ceoRole->givePermissionTo([
            // Analytics & Reporting (Full Access)
            'view analytics dashboard', 'view system reports', 'export system reports',
            'view performance metrics', 'view user analytics', 'view financial analytics',
            'view executive dashboard', 'view operational reports',

            // Financial Management (View & Export)
            'view financial reports', 'export financial reports',
            'view invoices', 'export invoices',
            'view exchange rates',

            // High-level Staff Management
            'view staff', 'view staff schedules',

            // Patient Management (Full Access)
            'view patients', 'create patients', 'edit patients', 'export patients',

            // Medical & Clinical Access
            'view medical records', 'view prescriptions', 'view appointments',
            'view visit services', 'view medical documents', 'download medical documents',

            // Marketing Analytics
            'view marketing analytics', 'export marketing reports',

            // System Health Monitoring
            'view system health', 'view system logs',

            // Communication
            'view messages', 'send messages', 'view notifications', 'create groups', 'manage group messages', 'export messages',

            // Personal
            'view own profile', 'edit own profile', 'change own password',
            'view public content',
        ]);

        // COO - Operational management with staff oversight
        $cooRole->givePermissionTo([
            // Staff Management (Full Access)
            'view staff', 'create staff', 'edit staff', 'export staff',
            'view staff schedules', 'manage staff schedules', 'approve leave requests',

            // Patient Management
            'view patients', 'create patients', 'edit patients', 'export patients',

            // Assignment Management
            'view assignments', 'create assignments', 'edit assignments', 'delete assignments',

            // Operational Reports
            'view analytics dashboard', 'view operational reports', 'export system reports',
            'view performance metrics',

            // Inventory Management
            'view inventory items', 'create inventory items', 'update inventory items',
            'view inventory requests', 'create inventory requests', 'update inventory requests',
            'view inventory transactions', 'view inventory alerts',
            'export inventory reports',

            // Event Management
            'view events', 'create events', 'edit events',
            'view event staff assignments', 'create event staff assignments', 'edit event staff assignments',

            // Communication
            'view messages', 'send messages', 'manage group messages',
            'view notifications', 'send notifications',

            // Personal
            'view own profile', 'edit own profile', 'change own password',
            'view public content',
        ]);

        // Admin - Administrative access with most permissions
        $adminRole->givePermissionTo([
            // Patient Management
            'view patients', 'create patients', 'edit patients', 'delete patients', 'export patients',

            // Staff Management (Limited)
            'view staff', 'edit staff', 'export staff',
            'view staff schedules',

            // User Management
            'view users', 'create users', 'edit users', 'export users',

            // Assignment Management
            'view assignments', 'create assignments', 'edit assignments', 'delete assignments',

            // Marketing & Campaign Management
            'view_any_campaign_contents', 'view_campaign_contents', 'create_campaign_contents', 'update_campaign_contents', 'delete_campaign_contents',
            'view_any_marketing_tasks', 'view_marketing_tasks', 'create_marketing_tasks', 'update_marketing_tasks', 'delete_marketing_tasks',
            'view marketing campaigns', 'create marketing campaigns', 'edit marketing campaigns',
            'view marketing leads', 'create marketing leads', 'edit marketing leads',
            'view marketing budgets', 'create marketing budgets', 'edit marketing budgets',

            // Insurance Management
            'view insurance companies', 'create insurance companies', 'update insurance companies',
            'view corporate clients', 'create corporate clients', 'update corporate clients',
            'view insurance policies', 'create insurance policies', 'update insurance policies',
            'view employee insurance records', 'create employee insurance records', 'update employee insurance records',
            'view insurance claims', 'create insurance claims', 'update insurance claims',

            // Inventory Management
            'view inventory items', 'create inventory items', 'update inventory items',
            'view inventory requests', 'create inventory requests', 'update inventory requests',
            'view inventory transactions', 'create inventory transactions', 'update inventory transactions',
            'view inventory alerts', 'create inventory alerts', 'update inventory alerts',
            'view inventory maintenance records', 'create inventory maintenance records', 'update inventory maintenance records',

            // Financial Management (Limited)
            'view invoices', 'create invoices', 'edit invoices',
            'view exchange rates', 'create exchange rates', 'update exchange rates',

            // Calendar & Events
            'view ethiopian calendar days', 'create ethiopian calendar days', 'update ethiopian calendar days',
            'view events', 'create events', 'edit events',
            'view eligibility criteria', 'create eligibility criteria', 'edit eligibility criteria',
            'view event recommendations', 'create event recommendations', 'edit event recommendations',
            'view event participants', 'create event participants', 'edit event participants',
            'view event staff assignments', 'create event staff assignments', 'edit event staff assignments',
            'view event broadcasts', 'create event broadcasts', 'edit event broadcasts',

            // Medical & Clinical
            'view medical records', 'create medical records', 'edit medical records',
            'view prescriptions', 'create prescriptions', 'edit prescriptions',
            'view appointments', 'create appointments', 'edit appointments', 'cancel appointments',
            'view visit services', 'create visit services', 'edit visit services',
            'view medical documents', 'upload medical documents', 'download medical documents',

            // Communication
            'view messages', 'send messages', 'delete messages',
            'view notifications', 'send notifications',

            // System (Limited)
            'view system settings',

            // Personal
            'view own profile', 'edit own profile', 'change own password',
            'view public content',
        ]);

        // Staff - Limited operational access
        $staffRole->givePermissionTo([
            // Patient Management (Limited)
            'view patients', 'edit patients',

            // Medical & Clinical (Core Functions)
            'view medical records', 'create medical records', 'edit medical records',
            'view prescriptions', 'create prescriptions', 'edit prescriptions',
            'view appointments', 'create appointments', 'edit appointments',
            'view visit services', 'create visit services', 'edit visit services',
            'view medical documents', 'upload medical documents', 'download medical documents',
            'view lab results', 'create lab results', 'edit lab results',

            // Assignment Management (View Own)
            'view assignments',

            // Inventory (Basic Access)
            'view inventory items',
            'view inventory requests', 'create inventory requests',
            'view inventory alerts',

            // Communication
            'view messages', 'send messages',
            'view notifications',

            // Events (Participation)
            'view events', 'view event participants',
            'view event staff assignments',

            // Personal
            'view own profile', 'edit own profile', 'change own password',
            'view public content',
        ]);

        // Guest - Very limited access to public content only
        $guestRole->givePermissionTo([
            // Public Access Only
            'view public content',
            'access guest features',

            // Basic Profile (if registered as guest)
            'view own profile', 'edit own profile', 'change own password',
        ]);
    }
}
