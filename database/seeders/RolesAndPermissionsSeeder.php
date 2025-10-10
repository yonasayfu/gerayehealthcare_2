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
            'view staff schedules', 'manage staff schedules',
            'view staff availabilities', 'create staff availabilities', 'edit staff availabilities', 'delete staff availabilities',
            'view leave requests', 'create leave requests', 'edit leave requests', 'approve leave requests',

            // User & Role Management
            'view users', 'create users', 'edit users', 'delete users', 'export users',
            'view roles', 'create roles', 'edit roles', 'delete roles',
            'assign roles', 'manage permissions', 'manage roles', 'manage users',

            // Assignment Management
            'view assignments', 'create assignments', 'edit assignments', 'delete assignments',

            // Task Delegation Management
            'view task delegations', 'create task delegations', 'edit task delegations', 'delete task delegations',

            // Marketing & Campaign Management
            'view_any_campaign_contents', 'view_campaign_contents', 'create_campaign_contents', 'update_campaign_contents', 'delete_campaign_contents', 'restore_campaign_contents', 'force_delete_campaign_contents',
            'view_any_marketing_tasks', 'view_marketing_tasks', 'create_marketing_tasks', 'update_marketing_tasks', 'delete_marketing_tasks', 'restore_marketing_tasks', 'force_delete_marketing_tasks',
            'view marketing campaigns', 'create marketing campaigns', 'edit marketing campaigns', 'delete marketing campaigns',
            'view marketing leads', 'create marketing leads', 'edit marketing leads', 'delete marketing leads',
            'view marketing budgets', 'create marketing budgets', 'edit marketing budgets', 'delete marketing budgets',
            'manage marketing',
            'view marketing analytics', 'export marketing reports',

            // Insurance Management
            'view insurance companies', 'create insurance companies', 'update insurance companies', 'edit insurance companies', 'delete insurance companies',
            'view corporate clients', 'create corporate clients', 'update corporate clients', 'edit corporate clients', 'delete corporate clients',
            'view insurance policies', 'create insurance policies', 'update insurance policies', 'edit insurance policies', 'delete insurance policies',
            'view employee insurance records', 'create employee insurance records', 'update employee insurance records', 'edit employee insurance records', 'delete employee insurance records',
            'view insurance claims', 'create insurance claims', 'update insurance claims', 'delete insurance claims',

            // Inventory Management
            'view inventory items', 'create inventory items', 'update inventory items', 'edit inventory items', 'delete inventory items',
            'view inventory requests', 'create inventory requests', 'update inventory requests', 'edit inventory requests', 'delete inventory requests',
            'view inventory transactions', 'create inventory transactions', 'update inventory transactions', 'edit inventory transactions', 'delete inventory transactions',
            'view inventory alerts', 'create inventory alerts', 'update inventory alerts', 'edit inventory alerts', 'delete inventory alerts',
            'view inventory maintenance records', 'create inventory maintenance records', 'update inventory maintenance records', 'edit inventory maintenance records', 'delete inventory maintenance records',
            'view suppliers', 'create suppliers', 'edit suppliers', 'delete suppliers',
            'export inventory reports', 'manage inventory suppliers',

            // Service Catalog
            'view services', 'create services', 'edit services', 'delete services',

            // Financial Management (Comprehensive)
            'view invoices', 'create invoices', 'edit invoices', 'delete invoices', 'export invoices',
            'view financial reports', 'export financial reports', 'manage budgets',
            'view staff payouts', 'create staff payouts', 'edit staff payouts', 'delete staff payouts', 'export staff payouts',
            'view financial analytics', 'view revenue reports', 'view expense reports',
            'manage financial settings', 'approve financial transactions', 'view audit logs',
            'manage payment methods', 'view cash flow', 'manage financial accounts',
            'view exchange rates', 'create exchange rates', 'update exchange rates', 'delete exchange rates',
            'reconcile payments',

            // Calendar & Events
            'view ethiopian calendar days', 'create ethiopian calendar days', 'update ethiopian calendar days', 'edit ethiopian calendar days', 'delete ethiopian calendar days',
            'view events', 'create events', 'edit events', 'delete events',
            'view eligibility criteria', 'create eligibility criteria', 'edit eligibility criteria', 'delete eligibility criteria',
            'view event recommendations', 'create event recommendations', 'edit event recommendations', 'delete event recommendations',
            'view event participants', 'create event participants', 'edit event participants', 'delete event participants',
            'view event staff assignments', 'create event staff assignments', 'edit event staff assignments', 'delete event staff assignments',
            'view event broadcasts', 'create event broadcasts', 'edit event broadcasts', 'delete event broadcasts',

            // Analytics & Reporting
            'view analytics dashboard', 'view system reports', 'export system reports',
            'view performance metrics', 'view user analytics', 'view financial analytics',
            'view executive dashboard', 'view operational reports', 'view reports',

            // System Administration
            'view system settings', 'edit system settings', 'manage system backups',
            'view system logs', 'manage system maintenance', 'view system health',

            // Productivity & Search
            'perform global search',

            // Communication & Messaging
            'view messages', 'send messages', 'delete messages', 'manage group messages', 'create groups', 'export messages',
            'view notifications', 'send notifications', 'manage notification settings',

            // Profile & Personal
            'view own profile', 'edit own profile', 'change own password',
            'view public content', 'access guest features',

            // Medical & Clinical (Healthcare specific)
            'view medical records', 'create medical records', 'edit medical records',
            'view prescriptions', 'create prescriptions', 'edit prescriptions', 'delete prescriptions',
            'view appointments', 'create appointments', 'edit appointments', 'cancel appointments',
            'view visit services', 'create visit services', 'edit visit services', 'delete visit services',
            'view medical documents', 'create medical documents', 'edit medical documents', 'delete medical documents', 'upload medical documents', 'download medical documents',
            'view lab results', 'create lab results', 'edit lab results', 'delete lab results',

            // Partnerships & Referrals
            'view partners', 'create partners', 'edit partners', 'delete partners',
            'view partner agreements', 'create partner agreements', 'edit partner agreements', 'delete partner agreements',
            'view partner commissions', 'create partner commissions', 'edit partner commissions', 'delete partner commissions',
            'view partner engagements', 'create partner engagements', 'edit partner engagements', 'delete partner engagements',
            'view referrals', 'create referrals', 'edit referrals', 'delete referrals',
            'view referral documents', 'create referral documents', 'edit referral documents', 'delete referral documents',
            'view shared invoices', 'create shared invoices', 'edit shared invoices', 'delete shared invoices',
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
        $doctorRole = Role::firstOrCreate(['name' => RoleEnum::DOCTOR->value]);
        $nurseRole = Role::firstOrCreate(['name' => RoleEnum::NURSE->value]);
        $staffRole = Role::firstOrCreate(['name' => RoleEnum::STAFF->value]);
        $patientRole = Role::firstOrCreate(['name' => RoleEnum::PATIENT->value]);
        $guestRole = Role::firstOrCreate(['name' => RoleEnum::GUEST->value]);

        // --- Assign Permissions to Roles ---
        // Super Admin gets all permissions automatically via a Gate in AuthServiceProvider.
        $superAdminRole->syncPermissions(Permission::all());

        // CEO - Executive level access with strategic oversight
        $ceoPermissions = [
            // Analytics & Reporting (Full Access)
            'view analytics dashboard', 'view system reports', 'export system reports',
            'view performance metrics', 'view user analytics', 'view financial analytics',
            'view executive dashboard', 'view operational reports', 'view reports',

            // Financial Management (View & Export)
            'view financial reports', 'export financial reports',
            'view invoices', 'export invoices',
            'view staff payouts', 'view revenue reports', 'view expense reports',
            'view exchange rates',

            // High-level Staff Management
            'view staff', 'view staff schedules', 'view staff availabilities',
            'view leave requests', 'edit leave requests', 'approve leave requests',

            // Patient Management (Full Access)
            'view patients', 'create patients', 'edit patients', 'export patients',

            // Medical & Clinical Access
            'view medical records', 'view prescriptions', 'view appointments',
            'view visit services', 'view medical documents', 'download medical documents', 'view lab results',

            // Insurance & Partnerships
            'view insurance companies', 'view corporate clients', 'view insurance policies', 'view insurance claims', 'view employee insurance records',
            'view ethiopian calendar days',
            'view partners', 'view partner agreements', 'view partner commissions', 'view partner engagements',
            'view referrals', 'view referral documents', 'view shared invoices',

            // Operations Oversight
            'view assignments', 'view services',
            'view inventory items', 'view inventory requests', 'view inventory transactions', 'view inventory alerts', 'view inventory maintenance records', 'view suppliers',

            // Marketing Analytics
            'view marketing analytics', 'export marketing reports',

            // System Health Monitoring
            'view system health', 'view system logs',

            // Enterprise Search
            'perform global search',

            // Communication
            'view messages', 'send messages', 'view notifications', 'create groups', 'manage group messages', 'export messages',

            // Task Delegation Management
            'view task delegations', 'create task delegations', 'edit task delegations', 'delete task delegations',

            // Personal
            'view own profile', 'edit own profile', 'change own password',
            'view public content',
        ];
        $ceoRole->syncPermissions($ceoPermissions);

        // COO - Operational management with staff oversight
        $cooPermissions = [
            // Staff Management (Full Access)
            'view staff', 'create staff', 'edit staff', 'export staff',
            'view staff schedules', 'manage staff schedules',
            'view staff availabilities', 'create staff availabilities', 'edit staff availabilities', 'delete staff availabilities',
            'view leave requests', 'edit leave requests', 'approve leave requests',

            // Patient Management
            'view patients', 'create patients', 'edit patients', 'export patients',

            // Assignment Management
            'view assignments', 'create assignments', 'edit assignments', 'delete assignments',

            // Task Delegation Management
            'view task delegations', 'create task delegations', 'edit task delegations', 'delete task delegations',

            // Clinical Operations
            'view medical records', 'create medical records', 'edit medical records',
            'view prescriptions', 'create prescriptions', 'edit prescriptions', 'delete prescriptions',
            'view visit services', 'create visit services', 'edit visit services', 'delete visit services',
            'view medical documents', 'create medical documents', 'edit medical documents', 'delete medical documents',
            'view lab results', 'create lab results', 'edit lab results',

            // Inventory & Suppliers
            'view inventory items', 'create inventory items', 'update inventory items', 'edit inventory items', 'delete inventory items',
            'view inventory requests', 'create inventory requests', 'update inventory requests', 'edit inventory requests', 'delete inventory requests',
            'view inventory transactions', 'create inventory transactions', 'update inventory transactions', 'edit inventory transactions', 'delete inventory transactions',
            'view inventory alerts', 'create inventory alerts', 'update inventory alerts', 'edit inventory alerts', 'delete inventory alerts',
            'view inventory maintenance records', 'create inventory maintenance records', 'update inventory maintenance records', 'edit inventory maintenance records', 'delete inventory maintenance records',
            'view suppliers', 'create suppliers', 'edit suppliers', 'delete suppliers',
            'manage inventory suppliers', 'export inventory reports',

            // Service Catalog
            'view services', 'create services', 'edit services',

            // Event Management
            'view events', 'create events', 'edit events', 'delete events',
            'view event staff assignments', 'create event staff assignments', 'edit event staff assignments', 'delete event staff assignments',
            'view event participants', 'create event participants', 'edit event participants', 'delete event participants',
            'view event recommendations', 'create event recommendations', 'edit event recommendations', 'delete event recommendations',
            'view event broadcasts', 'create event broadcasts', 'edit event broadcasts', 'delete event broadcasts',
            'view eligibility criteria', 'create eligibility criteria', 'edit eligibility criteria', 'delete eligibility criteria',
            'view ethiopian calendar days', 'create ethiopian calendar days', 'update ethiopian calendar days', 'delete ethiopian calendar days',

            // Insurance Operations
            'view insurance companies', 'create insurance companies', 'update insurance companies', 'edit insurance companies', 'delete insurance companies',
            'view corporate clients', 'create corporate clients', 'update corporate clients', 'edit corporate clients', 'delete corporate clients',
            'view insurance policies', 'create insurance policies', 'update insurance policies', 'edit insurance policies', 'delete insurance policies',
            'view insurance claims', 'create insurance claims', 'update insurance claims', 'delete insurance claims',
            'view employee insurance records', 'create employee insurance records', 'update employee insurance records', 'edit employee insurance records', 'delete employee insurance records',

            // Partnerships & Referrals
            'view partners', 'create partners', 'edit partners', 'delete partners',
            'view partner agreements', 'create partner agreements', 'edit partner agreements', 'delete partner agreements',
            'view partner commissions', 'create partner commissions', 'edit partner commissions', 'delete partner commissions',
            'view partner engagements', 'create partner engagements', 'edit partner engagements', 'delete partner engagements',
            'view referrals', 'create referrals', 'edit referrals', 'delete referrals',
            'view referral documents', 'create referral documents', 'edit referral documents', 'delete referral documents',
            'view shared invoices', 'create shared invoices', 'edit shared invoices', 'delete shared invoices',

            // Financial Operations
            'view invoices', 'create invoices', 'edit invoices', 'delete invoices',
            'view staff payouts', 'create staff payouts', 'edit staff payouts', 'delete staff payouts',
            'view financial reports', 'export financial reports', 'manage budgets',
            'view financial analytics', 'view revenue reports', 'view expense reports',
            'reconcile payments',

            // Marketing Insights
            'view analytics dashboard', 'view operational reports', 'export system reports', 'view performance metrics',
            'view marketing analytics', 'export marketing reports',

            // Search & Communication
            'perform global search',
            'view messages', 'send messages', 'manage group messages',
            'view notifications', 'send notifications',

            // Personal
            'view own profile', 'edit own profile', 'change own password',
            'view public content',
        ];
        $cooRole->syncPermissions($cooPermissions);

        // Admin - Administrative access with most permissions
        $adminPermissions = [
            // Patient Management
            'view patients', 'create patients', 'edit patients', 'delete patients', 'export patients',

            // Staff Management
            'view staff', 'create staff', 'edit staff', 'delete staff', 'export staff',
            'view staff schedules', 'manage staff schedules',
            'view staff availabilities', 'create staff availabilities', 'edit staff availabilities', 'delete staff availabilities',
            'view leave requests', 'create leave requests', 'edit leave requests', 'approve leave requests',

            // User & Role Management
            'view users', 'create users', 'edit users', 'delete users', 'export users',
            'view roles', 'create roles', 'edit roles', 'delete roles',
            'assign roles', 'manage roles', 'manage permissions', 'manage users',

            // Assignment Management
            'view assignments', 'create assignments', 'edit assignments', 'delete assignments',

            // Task Delegation Management
            'view task delegations', 'create task delegations', 'edit task delegations', 'delete task delegations',

            // Marketing & Campaign Management
            'view marketing campaigns', 'create marketing campaigns', 'edit marketing campaigns', 'delete marketing campaigns',
            'view marketing leads', 'create marketing leads', 'edit marketing leads', 'delete marketing leads',
            'view marketing budgets', 'create marketing budgets', 'edit marketing budgets', 'delete marketing budgets',
            'manage marketing',
            'view_any_campaign_contents', 'view_campaign_contents', 'create_campaign_contents', 'update_campaign_contents', 'delete_campaign_contents', 'restore_campaign_contents', 'force_delete_campaign_contents',
            'view_any_marketing_tasks', 'view_marketing_tasks', 'create_marketing_tasks', 'update_marketing_tasks', 'delete_marketing_tasks', 'restore_marketing_tasks', 'force_delete_marketing_tasks',
            'view marketing analytics', 'export marketing reports',

            // Clinical & Medical Operations
            'view medical records', 'create medical records', 'edit medical records',
            'view prescriptions', 'create prescriptions', 'edit prescriptions', 'delete prescriptions',
            'view appointments', 'create appointments', 'edit appointments', 'cancel appointments',
            'view visit services', 'create visit services', 'edit visit services', 'delete visit services',
            'view medical documents', 'create medical documents', 'edit medical documents', 'delete medical documents', 'upload medical documents', 'download medical documents',
            'view lab results', 'create lab results', 'edit lab results', 'delete lab results',

            // Inventory & Suppliers
            'view inventory items', 'create inventory items', 'update inventory items', 'edit inventory items', 'delete inventory items',
            'view inventory requests', 'create inventory requests', 'update inventory requests', 'edit inventory requests', 'delete inventory requests',
            'view inventory transactions', 'create inventory transactions', 'update inventory transactions', 'edit inventory transactions', 'delete inventory transactions',
            'view inventory alerts', 'create inventory alerts', 'update inventory alerts', 'edit inventory alerts', 'delete inventory alerts',
            'view inventory maintenance records', 'create inventory maintenance records', 'update inventory maintenance records', 'edit inventory maintenance records', 'delete inventory maintenance records',
            'view suppliers', 'create suppliers', 'edit suppliers', 'delete suppliers',
            'manage inventory suppliers', 'export inventory reports',

            // Service Catalog
            'view services', 'create services', 'edit services', 'delete services',

            // Insurance Management
            'view insurance companies', 'create insurance companies', 'update insurance companies', 'edit insurance companies', 'delete insurance companies',
            'view corporate clients', 'create corporate clients', 'update corporate clients', 'edit corporate clients', 'delete corporate clients',
            'view insurance policies', 'create insurance policies', 'update insurance policies', 'edit insurance policies', 'delete insurance policies',
            'view insurance claims', 'create insurance claims', 'update insurance claims', 'delete insurance claims',
            'view employee insurance records', 'create employee insurance records', 'update employee insurance records', 'edit employee insurance records', 'delete employee insurance records',

            // Partnerships & Referrals
            'view partners', 'create partners', 'edit partners', 'delete partners',
            'view partner agreements', 'create partner agreements', 'edit partner agreements', 'delete partner agreements',
            'view partner commissions', 'create partner commissions', 'edit partner commissions', 'delete partner commissions',
            'view partner engagements', 'create partner engagements', 'edit partner engagements', 'delete partner engagements',
            'view referrals', 'create referrals', 'edit referrals', 'delete referrals',
            'view referral documents', 'create referral documents', 'edit referral documents', 'delete referral documents',
            'view shared invoices', 'create shared invoices', 'edit shared invoices', 'delete shared invoices',

            // Event Management
            'view ethiopian calendar days', 'create ethiopian calendar days', 'update ethiopian calendar days', 'edit ethiopian calendar days', 'delete ethiopian calendar days',
            'view events', 'create events', 'edit events', 'delete events',
            'view eligibility criteria', 'create eligibility criteria', 'edit eligibility criteria', 'delete eligibility criteria',
            'view event recommendations', 'create event recommendations', 'edit event recommendations', 'delete event recommendations',
            'view event participants', 'create event participants', 'edit event participants', 'delete event participants',
            'view event staff assignments', 'create event staff assignments', 'edit event staff assignments', 'delete event staff assignments',
            'view event broadcasts', 'create event broadcasts', 'edit event broadcasts', 'delete event broadcasts',

            // Financial Management
            'view invoices', 'create invoices', 'edit invoices', 'delete invoices', 'export invoices',
            'view staff payouts', 'create staff payouts', 'edit staff payouts', 'delete staff payouts', 'export staff payouts',
            'view financial reports', 'export financial reports', 'manage budgets',
            'view financial analytics', 'view revenue reports', 'view expense reports',
            'view exchange rates', 'create exchange rates', 'update exchange rates', 'delete exchange rates',
            'manage payment methods', 'manage financial settings', 'view cash flow', 'manage financial accounts',
            'view audit logs', 'approve financial transactions', 'reconcile payments',

            // Reporting & Search
            'view analytics dashboard', 'view system reports', 'export system reports',
            'view performance metrics', 'view user analytics', 'view operational reports', 'view reports',
            'perform global search',

            // Communication
            'view messages', 'send messages', 'delete messages', 'manage group messages', 'create groups', 'export messages',
            'view notifications', 'send notifications',

            // System Administration (Core)
            'view system settings', 'edit system settings', 'manage system backups',
            'view system logs', 'manage system maintenance', 'view system health',

            // Personal
            'view own profile', 'edit own profile', 'change own password',
            'view public content',
        ];
        $adminRole->syncPermissions($adminPermissions);

        // Staff - Limited operational access
        $staffPermissions = [
            // Patient Management (Limited)
            'view patients', 'edit patients',

            // Leave Management
            'create leave requests',

            // Medical & Clinical (Core Functions)
            'view medical records', 'create medical records', 'edit medical records',
            'view prescriptions', 'create prescriptions', 'edit prescriptions',
            'view appointments', 'create appointments', 'edit appointments',
            'view visit services', 'create visit services', 'edit visit services',
            'view medical documents', 'create medical documents', 'edit medical documents', 'upload medical documents', 'download medical documents',
            'view lab results', 'create lab results', 'edit lab results',

            // Assignment Management (View Own)
            'view assignments',

            // Task Delegation Management (View Own)
            'view task delegations',

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
        ];
        $staffRole->syncPermissions($staffPermissions);

        // Doctor - Clinical leadership access
        $doctorPermissions = [
            'view patients', 'create patients', 'edit patients', 'export patients',
            'view medical records', 'create medical records', 'edit medical records',
            'view prescriptions', 'create prescriptions', 'edit prescriptions', 'delete prescriptions',
            'view appointments', 'create appointments', 'edit appointments', 'cancel appointments',
            'view visit services', 'create visit services', 'edit visit services', 'delete visit services',
            'view medical documents', 'create medical documents', 'edit medical documents', 'upload medical documents', 'download medical documents', 'delete medical documents',
            'view lab results', 'create lab results', 'edit lab results',
            'view assignments', 'create assignments', 'edit assignments',
            'view task delegations', 'create task delegations', 'edit task delegations',
            'view inventory items', 'view inventory alerts',
            'view messages', 'send messages', 'manage group messages', 'create groups',
            'view notifications', 'send notifications',
            'view events', 'view event participants', 'create event participants', 'edit event participants', 'view event staff assignments',
            'view invoices', 'view financial reports',
            'view analytics dashboard', 'view system reports',
            'view own profile', 'edit own profile', 'change own password', 'view public content',
        ];
        $doctorRole->syncPermissions($doctorPermissions);

        // Nurse - Enhanced clinical operations
        $nursePermissions = [
            'view patients', 'edit patients',
            'create leave requests',
            'view medical records', 'create medical records', 'edit medical records',
            'view prescriptions', 'create prescriptions', 'edit prescriptions',
            'view appointments', 'create appointments', 'edit appointments',
            'view visit services', 'create visit services', 'edit visit services',
            'view medical documents', 'create medical documents', 'edit medical documents', 'upload medical documents', 'download medical documents',
            'view lab results', 'create lab results', 'edit lab results',
            'view assignments',
            'view task delegations',
            'view inventory items', 'view inventory requests', 'create inventory requests', 'view inventory alerts',
            'view messages', 'send messages',
            'view notifications',
            'view events', 'view event participants', 'view event staff assignments',
            'view invoices',
            'view own profile', 'edit own profile', 'change own password', 'view public content',
        ];
        $nurseRole->syncPermissions($nursePermissions);

        // Patient - Self-service portal access
        $patientPermissions = [
            'view patients',
            'view visit services',
            'view medical records',
            'view prescriptions',
            'view appointments',
            'view medical documents', 'download medical documents', 'upload medical documents',
            'view invoices', 'view shared invoices',
            'view messages', 'send messages',
            'view notifications',
            'view events',
            'view own profile', 'edit own profile', 'change own password',
            'view public content',
        ];
        $patientRole->syncPermissions($patientPermissions);

        // Guest - Very limited access to public content only
        $guestPermissions = [
            // Public Access Only
            'view public content',
            'access guest features',

            // Basic Profile (if registered as guest)
            'view own profile', 'edit own profile', 'change own password',
        ];
        $guestRole->syncPermissions($guestPermissions);
    }
}
