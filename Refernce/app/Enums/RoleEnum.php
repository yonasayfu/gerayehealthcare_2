<?php

namespace App\Enums;

enum RoleEnum: string
{
    case SUPER_ADMIN = 'super-admin';
    case ADMIN = 'admin';
    case STAFF = 'staff';
    case USER = 'user';

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
            self::ADMIN => 'Administrator',
            self::STAFF => 'Staff Member',
            self::USER => 'User',
        };
    }

    /**
     * Get role description
     */
    public function getDescription(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Full system access with all permissions',
            self::ADMIN => 'Administrative access with most permissions',
            self::STAFF => 'Staff-level access with limited permissions',
            self::USER => 'Basic user access with minimal permissions',
        };
    }

    /**
     * Check if role is admin level or higher
     */
    public function isAdminLevel(): bool
    {
        return in_array($this, [self::SUPER_ADMIN, self::ADMIN]);
    }
}
