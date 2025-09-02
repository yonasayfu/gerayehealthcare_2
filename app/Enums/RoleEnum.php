<?php

namespace App\Enums;

enum RoleEnum: string
{
    case SUPER_ADMIN = 'super-admin';
    case CEO = 'ceo';
    case COO = 'coo';
    case ADMIN = 'admin';
    case STAFF = 'staff';
    case GUEST = 'guest';

    /**
     * Get all role values as an array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get role display name
     */
    public function getDisplayName(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super Administrator',
            self::CEO => 'Chief Executive Officer',
            self::COO => 'Chief Operating Officer',
            self::ADMIN => 'Administrator',
            self::STAFF => 'Staff Member',
            self::GUEST => 'Guest User',
        };
    }

    /**
     * Get role description
     */
    public function getDescription(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Full system access with all permissions',
            self::CEO => 'Executive access with strategic oversight and reporting',
            self::COO => 'Operational access with staff management and reporting',
            self::ADMIN => 'Administrative access with most permissions',
            self::STAFF => 'Staff-level access with limited permissions',
            self::GUEST => 'Limited access to public content only',
        };
    }

    /**
     * Check if role is admin level or higher
     */
    public function isAdminLevel(): bool
    {
        return in_array($this, [self::SUPER_ADMIN, self::CEO, self::COO, self::ADMIN]);
    }

    /**
     * Check if role is executive level
     */
    public function isExecutiveLevel(): bool
    {
        return in_array($this, [self::SUPER_ADMIN, self::CEO, self::COO]);
    }

    /**
     * Get role hierarchy level (higher number = more permissions)
     */
    public function getHierarchyLevel(): int
    {
        return match ($this) {
            self::SUPER_ADMIN => 100,
            self::CEO => 90,
            self::COO => 80,
            self::ADMIN => 70,
            self::STAFF => 50,
            self::GUEST => 10,
        };
    }

    /**
     * Check if this role can manage another role
     */
    public function canManage(self $otherRole): bool
    {
        return $this->getHierarchyLevel() > $otherRole->getHierarchyLevel();
    }
}
