<?php

namespace App\Support;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Support\Collection;

class ModuleAccess
{
    private const ACCESS_LEVELS = [
        'none' => 0,
        'view' => 1,
        'manage' => 2,
        'full' => 3,
    ];

    /**
     * High-level module definitions used by web + mobile to highlight features per role.
     */
    public static function definitions(): array
    {
        return [
            'dashboards' => [
                'label' => 'Dashboards & Analytics',
                'description' => 'KPI dashboards, executive summaries, and trend analysis.',
                'permissions' => [
                    'view' => ['view analytics dashboard', 'view system reports'],
                    'manage' => ['export system reports'],
                    'full' => ['view executive dashboard', 'view operational reports', 'view marketing analytics'],
                ],
                'role_access' => [
                    RoleEnum::SUPER_ADMIN->value => 'full',
                    RoleEnum::CEO->value => 'view',
                    RoleEnum::COO->value => 'full',
                    RoleEnum::ADMIN->value => 'full',
                    RoleEnum::DOCTOR->value => 'view',
                    RoleEnum::NURSE->value => 'view',
                    RoleEnum::STAFF->value => 'view',
                ],
            ],
            'patients' => [
                'label' => 'Patient Management',
                'description' => 'Patient roster, assignments, care journeys, and exports.',
                'permissions' => [
                    'view' => ['view patients'],
                    'manage' => ['create patients', 'edit patients'],
                    'full' => ['delete patients', 'export patients'],
                ],
                'role_access' => [
                    RoleEnum::SUPER_ADMIN->value => 'full',
                    RoleEnum::CEO->value => 'view',
                    RoleEnum::COO->value => 'full',
                    RoleEnum::ADMIN->value => 'full',
                    RoleEnum::DOCTOR->value => 'manage',
                    RoleEnum::NURSE->value => 'view',
                    RoleEnum::STAFF->value => 'view',
                    RoleEnum::PATIENT->value => 'view',
                ],
            ],
            'staff-ops' => [
                'label' => 'Staff Management',
                'description' => 'Roster, availability, scheduling, and leave approvals.',
                'permissions' => [
                    'view' => ['view staff', 'view staff schedules', 'view staff availabilities', 'view leave requests'],
                    'manage' => ['create staff', 'edit staff', 'manage staff schedules', 'create staff availabilities', 'edit staff availabilities', 'approve leave requests'],
                    'full' => ['delete staff', 'delete staff availabilities'],
                ],
                'role_access' => [
                    RoleEnum::SUPER_ADMIN->value => 'full',
                    RoleEnum::CEO->value => 'view',
                    RoleEnum::COO->value => 'full',
                    RoleEnum::ADMIN->value => 'manage',
                    RoleEnum::DOCTOR->value => 'view',
                    RoleEnum::NURSE->value => 'manage',
                    RoleEnum::STAFF->value => 'view',
                ],
            ],
            'tasking' => [
                'label' => 'Tasking & Assignments',
                'description' => 'Caregiver assignments, task delegations, and to-do boards.',
                'permissions' => [
                    'view' => ['view assignments', 'view task delegations'],
                    'manage' => ['create assignments', 'edit assignments', 'create task delegations', 'edit task delegations'],
                    'full' => ['delete assignments', 'delete task delegations'],
                ],
                'role_access' => [
                    RoleEnum::SUPER_ADMIN->value => 'full',
                    RoleEnum::CEO->value => 'manage',
                    RoleEnum::COO->value => 'full',
                    RoleEnum::ADMIN->value => 'full',
                    RoleEnum::DOCTOR->value => 'manage',
                    RoleEnum::NURSE->value => 'manage',
                    RoleEnum::STAFF->value => 'view',
                    RoleEnum::PATIENT->value => 'view',
                ],
            ],
            'clinical' => [
                'label' => 'Clinical Records',
                'description' => 'Visit services, prescriptions, lab results, and appointments.',
                'permissions' => [
                    'view' => ['view visit services', 'view prescriptions', 'view lab results', 'view appointments'],
                    'manage' => ['create visit services', 'edit visit services', 'create prescriptions', 'edit prescriptions', 'create lab results', 'edit lab results'],
                    'full' => ['delete visit services', 'delete prescriptions', 'delete lab results'],
                ],
                'role_access' => [
                    RoleEnum::SUPER_ADMIN->value => 'full',
                    RoleEnum::COO->value => 'full',
                    RoleEnum::ADMIN->value => 'full',
                    RoleEnum::DOCTOR->value => 'full',
                    RoleEnum::NURSE->value => 'manage',
                    RoleEnum::STAFF->value => 'manage',
                    RoleEnum::PATIENT->value => 'view',
                ],
            ],
            'medical-documents' => [
                'label' => 'Medical Documents',
                'description' => 'Secure upload, review, and sharing of clinical documentation.',
                'permissions' => [
                    'view' => ['view medical documents', 'download medical documents'],
                    'manage' => ['create medical documents', 'edit medical documents', 'upload medical documents'],
                    'full' => ['delete medical documents'],
                ],
                'role_access' => [
                    RoleEnum::SUPER_ADMIN->value => 'full',
                    RoleEnum::COO->value => 'full',
                    RoleEnum::ADMIN->value => 'full',
                    RoleEnum::DOCTOR->value => 'full',
                    RoleEnum::NURSE->value => 'manage',
                    RoleEnum::STAFF->value => 'manage',
                    RoleEnum::PATIENT->value => 'view',
                ],
            ],
            'inventory' => [
                'label' => 'Inventory & Suppliers',
                'description' => 'Procurement, stock levels, maintenance records, and alerts.',
                'permissions' => [
                    'view' => ['view inventory items', 'view inventory requests', 'view inventory alerts', 'view inventory maintenance records', 'view inventory transactions', 'view suppliers'],
                    'manage' => ['create inventory items', 'update inventory items', 'create inventory requests', 'update inventory requests', 'create inventory transactions', 'update inventory transactions', 'create suppliers', 'edit suppliers', 'manage inventory suppliers'],
                    'full' => ['delete inventory items', 'delete inventory requests', 'delete inventory transactions', 'delete inventory alerts', 'delete inventory maintenance records', 'delete suppliers'],
                ],
                'role_access' => [
                    RoleEnum::SUPER_ADMIN->value => 'full',
                    RoleEnum::COO->value => 'full',
                    RoleEnum::ADMIN->value => 'full',
                    RoleEnum::DOCTOR->value => 'view',
                    RoleEnum::NURSE->value => 'view',
                    RoleEnum::STAFF->value => 'manage',
                ],
            ],
            'services' => [
                'label' => 'Service Catalog',
                'description' => 'Manage offerings and pricing for in-home services.',
                'permissions' => [
                    'view' => ['view services'],
                    'manage' => ['create services', 'edit services'],
                    'full' => ['delete services'],
                ],
                'role_access' => [
                    RoleEnum::SUPER_ADMIN->value => 'full',
                    RoleEnum::COO->value => 'manage',
                    RoleEnum::ADMIN->value => 'manage',
                    RoleEnum::DOCTOR->value => 'view',
                    RoleEnum::NURSE->value => 'view',
                    RoleEnum::STAFF->value => 'view',
                ],
            ],
            'insurance' => [
                'label' => 'Insurance Suite',
                'description' => 'Insurance companies, policies, claims, and employee benefits.',
                'permissions' => [
                    'view' => ['view insurance companies', 'view insurance policies', 'view insurance claims', 'view employee insurance records', 'view corporate clients', 'view ethiopian calendar days'],
                    'manage' => ['create insurance companies', 'update insurance companies', 'create insurance policies', 'update insurance policies', 'create insurance claims', 'update insurance claims', 'create employee insurance records', 'update employee insurance records', 'create corporate clients', 'update corporate clients'],
                    'full' => ['delete insurance companies', 'delete insurance policies', 'delete insurance claims', 'delete employee insurance records', 'delete corporate clients', 'delete ethiopian calendar days'],
                ],
                'role_access' => [
                    RoleEnum::SUPER_ADMIN->value => 'full',
                    RoleEnum::COO->value => 'manage',
                    RoleEnum::ADMIN->value => 'manage',
                    RoleEnum::DOCTOR->value => 'view',
                    RoleEnum::NURSE->value => 'view',
                    RoleEnum::CEO->value => 'view',
                    RoleEnum::PATIENT->value => 'view',
                ],
            ],
            'partnerships' => [
                'label' => 'Partnerships & Referrals',
                'description' => 'Partner lifecycle, referral tracking, commissions, and shared invoices.',
                'permissions' => [
                    'view' => ['view partners', 'view partner agreements', 'view partner commissions', 'view partner engagements', 'view referrals', 'view referral documents', 'view shared invoices'],
                    'manage' => ['create partners', 'edit partners', 'create partner agreements', 'edit partner agreements', 'create partner commissions', 'edit partner commissions', 'create partner engagements', 'edit partner engagements', 'create referrals', 'edit referrals', 'create referral documents', 'edit referral documents', 'create shared invoices', 'edit shared invoices'],
                    'full' => ['delete partners', 'delete partner agreements', 'delete partner commissions', 'delete partner engagements', 'delete referrals', 'delete referral documents', 'delete shared invoices'],
                ],
                'role_access' => [
                    RoleEnum::SUPER_ADMIN->value => 'full',
                    RoleEnum::COO->value => 'manage',
                    RoleEnum::ADMIN->value => 'manage',
                    RoleEnum::DOCTOR->value => 'view',
                    RoleEnum::NURSE->value => 'view',
                    RoleEnum::CEO->value => 'view',
                ],
            ],
            'marketing' => [
                'label' => 'Marketing',
                'description' => 'Campaign planning, lead management, budgets, and ROI analytics.',
                'permissions' => [
                    'view' => ['view marketing campaigns', 'view marketing leads', 'view marketing budgets', 'view marketing analytics', 'view_any_campaign_contents', 'view_any_marketing_tasks'],
                    'manage' => ['create marketing campaigns', 'edit marketing campaigns', 'create marketing leads', 'edit marketing leads', 'create marketing budgets', 'edit marketing budgets', 'manage marketing', 'create_campaign_contents', 'update_campaign_contents', 'create_marketing_tasks', 'update_marketing_tasks'],
                    'full' => ['delete marketing campaigns', 'delete marketing leads', 'delete marketing budgets', 'delete_campaign_contents', 'delete_marketing_tasks'],
                ],
                'role_access' => [
                    RoleEnum::SUPER_ADMIN->value => 'full',
                    RoleEnum::ADMIN->value => 'full',
                    RoleEnum::CEO->value => 'view',
                    RoleEnum::COO->value => 'manage',
                ],
            ],
            'events' => [
                'label' => 'Events & Outreach',
                'description' => 'Events, eligibility criteria, recommendations, participants, broadcasts.',
                'permissions' => [
                    'view' => ['view events', 'view eligibility criteria', 'view event recommendations', 'view event participants', 'view event staff assignments', 'view event broadcasts'],
                    'manage' => ['create events', 'edit events', 'create eligibility criteria', 'edit eligibility criteria', 'create event recommendations', 'edit event recommendations', 'create event participants', 'edit event participants', 'create event staff assignments', 'edit event staff assignments', 'create event broadcasts', 'edit event broadcasts'],
                    'full' => ['delete events', 'delete eligibility criteria', 'delete event recommendations', 'delete event participants', 'delete event staff assignments', 'delete event broadcasts'],
                ],
                'role_access' => [
                    RoleEnum::SUPER_ADMIN->value => 'full',
                    RoleEnum::COO->value => 'full',
                    RoleEnum::ADMIN->value => 'full',
                    RoleEnum::DOCTOR->value => 'manage',
                    RoleEnum::NURSE->value => 'view',
                    RoleEnum::STAFF->value => 'view',
                ],
            ],
            'financial' => [
                'label' => 'Financial',
                'description' => 'Invoices, payouts, budgets, reconciliations, and financial analytics.',
                'permissions' => [
                    'view' => ['view invoices', 'view staff payouts', 'view financial reports', 'view revenue reports', 'view expense reports', 'view financial analytics'],
                    'manage' => ['create invoices', 'edit invoices', 'create staff payouts', 'edit staff payouts', 'manage budgets', 'manage financial settings', 'manage payment methods', 'manage financial accounts'],
                    'full' => ['delete invoices', 'delete staff payouts', 'approve financial transactions', 'reconcile payments', 'export financial reports', 'export invoices', 'export staff payouts'],
                ],
                'role_access' => [
                    RoleEnum::SUPER_ADMIN->value => 'full',
                    RoleEnum::CEO->value => 'view',
                    RoleEnum::COO->value => 'manage',
                    RoleEnum::ADMIN->value => 'full',
                    RoleEnum::DOCTOR->value => 'view',
                    RoleEnum::NURSE->value => 'view',
                    RoleEnum::STAFF->value => 'view',
                    RoleEnum::PATIENT->value => 'view',
                ],
            ],
            'reports' => [
                'label' => 'Reports & Global Search',
                'description' => 'Unified reporting hub and cross-module search tooling.',
                'permissions' => [
                    'view' => ['view reports', 'perform global search'],
                    'manage' => ['export system reports'],
                    'full' => [],
                ],
                'role_access' => [
                    RoleEnum::SUPER_ADMIN->value => 'full',
                    RoleEnum::CEO->value => 'view',
                    RoleEnum::COO->value => 'full',
                    RoleEnum::ADMIN->value => 'full',
                    RoleEnum::DOCTOR->value => 'view',
                    RoleEnum::NURSE->value => 'view',
                ],
            ],
            'communication' => [
                'label' => 'Communication',
                'description' => 'Direct messages, notifications, and group collaboration.',
                'permissions' => [
                    'view' => ['view messages', 'view notifications'],
                    'manage' => ['send messages', 'send notifications', 'manage group messages', 'create groups'],
                    'full' => ['delete messages', 'export messages'],
                ],
                'role_access' => [
                    RoleEnum::SUPER_ADMIN->value => 'full',
                    RoleEnum::CEO->value => 'full',
                    RoleEnum::COO->value => 'full',
                    RoleEnum::ADMIN->value => 'full',
                    RoleEnum::DOCTOR->value => 'full',
                    RoleEnum::NURSE->value => 'manage',
                    RoleEnum::STAFF->value => 'manage',
                    RoleEnum::PATIENT->value => 'manage',
                ],
            ],
            'system' => [
                'label' => 'System Administration',
                'description' => 'Platform configuration, roles, logs, and maintenance tasks.',
                'permissions' => [
                    'view' => ['view system health', 'view system logs', 'view system settings'],
                    'manage' => ['edit system settings', 'manage system backups', 'manage system maintenance', 'manage roles', 'manage users'],
                    'full' => ['manage permissions'],
                ],
                'role_access' => [
                    RoleEnum::SUPER_ADMIN->value => 'full',
                    RoleEnum::CEO->value => 'view',
                    RoleEnum::COO->value => 'manage',
                    RoleEnum::ADMIN->value => 'manage',
                ],
            ],
        ];
    }

    public static function forUser(User $user): array
    {
        $user->loadMissing('roles', 'permissions');

        $roles = $user->getRoleNames()->map(fn ($role) => strtolower($role))->all();
        $permissions = $user->getAllPermissions()->pluck('name');

        return collect(self::definitions())
            ->map(function (array $module, string $key) use ($roles, $permissions) {
                $level = self::resolveByRole($module['role_access'] ?? [], $roles);

                if ($level === 'none') {
                    $level = self::resolveByPermissions($module['permissions'] ?? [], $permissions);
                }

                if ($level === 'none') {
                    return null;
                }

                return [
                    'key' => $key,
                    'label' => $module['label'],
                    'description' => $module['description'],
                    'access_level' => $level,
                    'actions' => array_filter([
                        'view' => $module['permissions']['view'] ?? [],
                        'manage' => $module['permissions']['manage'] ?? [],
                        'full' => $module['permissions']['full'] ?? [],
                    ]),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    private static function resolveByRole(array $roleAccess, array $roles): string
    {
        $current = 'none';

        foreach ($roles as $role) {
            if (isset($roleAccess[$role])) {
                $candidate = $roleAccess[$role];
                if ((self::ACCESS_LEVELS[$candidate] ?? 0) > self::ACCESS_LEVELS[$current]) {
                    $current = $candidate;
                }
            }
        }

        return $current;
    }

    private static function resolveByPermissions(array $permissionMap, Collection $userPermissions): string
    {
        foreach (['full', 'manage', 'view'] as $level) {
            $required = $permissionMap[$level] ?? [];
            if (empty($required)) {
                continue;
            }

            $required = collect($required)->filter();
            if ($required->isEmpty()) {
                continue;
            }

            if ($required->every(fn ($perm) => $userPermissions->contains($perm))) {
                return $level;
            }
        }

        return 'none';
    }
}
