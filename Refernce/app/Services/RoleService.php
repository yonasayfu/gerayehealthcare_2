<?php

namespace App\Services;

use App\Enums\RoleEnum;
use App\Exceptions\BusinessException;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleService extends PerformanceOptimizedBaseService
{
    protected array $protectedRoles = ['super-admin', 'admin', 'staff', 'user'];

    public function __construct(Role $role)
    {
        parent::__construct($role);
    }

    /**
     * Create a new role with permissions
     */
    public function create(array|object $data): Role
    {
        $data = is_object($data) ? (array) $data : $data;

        $role = Role::create([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'] ?? 'web',
        ]);

        if (isset($data['permissions']) && is_array($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        $this->clearCaches();

        return $role;
    }

    /**
     * Update role with permissions
     */
    public function update(int $id, array|object $data): Role
    {
        $data = is_object($data) ? (array) $data : $data;
        $role = $this->getById($id);

        // Prevent updating protected roles
        if (in_array($role->name, $this->protectedRoles)) {
            throw new BusinessException("Cannot modify protected role: {$role->name}");
        }

        $role->update(['name' => $data['name']]);

        if (isset($data['permissions']) && is_array($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        $this->clearCaches();

        return $role;
    }

    /**
     * Delete role (with protection for system roles)
     */
    public function delete(int $id): ?bool
    {
        $role = $this->getById($id);

        if (in_array($role->name, $this->protectedRoles)) {
            throw new BusinessException("Cannot delete protected role: {$role->name}");
        }

        // Check if role is assigned to users
        if ($role->users()->count() > 0) {
            throw new BusinessException('Cannot delete role that is assigned to users');
        }

        $result = parent::delete($id);
        $this->clearCaches();

        return $result;
    }

    /**
     * Get all permissions grouped by category
     */
    public function getPermissionsGrouped(): Collection
    {
        return $this->getCachedData('permissions_grouped', function () {
            return Permission::all()->groupBy(function ($permission) {
                $parts = explode('-', $permission->name);

                return ucfirst($parts[0] ?? 'General');
            });
        });
    }

    /**
     * Get role with permissions
     */
    public function getRoleWithPermissions(int $id): Role
    {
        return $this->getCachedData("role_with_permissions_{$id}", function () use ($id) {
            return Role::with('permissions')->findOrFail($id);
        });
    }

    /**
     * Assign role to user
     */
    public function assignRoleToUser(int $userId, string $roleName): void
    {
        $user = \App\Models\User::findOrFail($userId);
        $user->assignRole($roleName);

        $this->clearUserCaches($userId);
    }

    /**
     * Remove role from user
     */
    public function removeRoleFromUser(int $userId, string $roleName): void
    {
        $user = \App\Models\User::findOrFail($userId);
        $user->removeRole($roleName);

        $this->clearUserCaches($userId);
    }

    /**
     * Get users with specific role
     */
    public function getUsersWithRole(string $roleName): Collection
    {
        return $this->getCachedData("users_with_role_{$roleName}", function () use ($roleName) {
            $role = Role::findByName($roleName);

            return $role->users;
        });
    }

    /**
     * Initialize default roles and permissions
     */
    public function initializeDefaultRoles(): void
    {
        $this->createDefaultPermissions();
        $this->createDefaultRoles();
    }

    /**
     * Create default permissions
     */
    private function createDefaultPermissions(): void
    {
        $permissions = [
            // User Management
            'view-users', 'create-users', 'edit-users', 'delete-users',

            // Role Management
            'view-roles', 'create-roles', 'edit-roles', 'delete-roles',

            // Staff Management
            'view-staff', 'create-staff', 'edit-staff', 'delete-staff',

            // Reports
            'view-reports', 'export-reports',

            // System
            'view-admin-dashboard', 'manage-settings',

            // Messages
            'view-messages', 'send-messages', 'delete-messages',

            // Notifications
            'view-notifications', 'manage-notifications',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }

    /**
     * Create default roles with permissions
     */
    private function createDefaultRoles(): void
    {
        // Super Admin - All permissions
        $superAdmin = Role::firstOrCreate(['name' => RoleEnum::SUPER_ADMIN->value]);
        $superAdmin->syncPermissions(Permission::all());

        // Admin - Most permissions except user/role management
        $admin = Role::firstOrCreate(['name' => RoleEnum::ADMIN->value]);
        $adminPermissions = [
            'view-users', 'view-staff', 'create-staff', 'edit-staff',
            'view-reports', 'export-reports', 'view-admin-dashboard',
            'view-messages', 'send-messages', 'view-notifications',
        ];
        $admin->syncPermissions($adminPermissions);

        // Staff - Limited permissions
        $staff = Role::firstOrCreate(['name' => RoleEnum::STAFF->value]);
        $staffPermissions = [
            'view-messages', 'send-messages', 'view-notifications',
        ];
        $staff->syncPermissions($staffPermissions);

        // User - Basic permissions
        $user = Role::firstOrCreate(['name' => RoleEnum::USER->value]);
        $userPermissions = [
            'view-messages', 'send-messages', 'view-notifications',
        ];
        $user->syncPermissions($userPermissions);
    }

    /**
     * Clear user-specific caches
     */
    private function clearUserCaches(int $userId): void
    {
        $this->clearCache("user_roles_{$userId}");
        $this->clearCache("user_permissions_{$userId}");
    }

    /**
     * Clear all role-related caches
     */
    protected function clearCaches(): void
    {
        parent::clearCaches();
        $this->clearCache('permissions_grouped');
        $this->clearCache('roles_with_permissions');
    }
}
