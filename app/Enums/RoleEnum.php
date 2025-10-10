<?php

namespace App\Enums;

enum RoleEnum: string
{
    case SUPER_ADMIN = 'super-admin';
    case CEO = 'ceo';
    case COO = 'coo';
    case ADMIN = 'admin';
    case DOCTOR = 'doctor';
    case NURSE = 'nurse';
    case STAFF = 'staff';
    case PATIENT = 'patient';
    case GUEST = 'guest';

    /**
     * Get all role values as an array.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get role display name.
     */
    public function getDisplayName(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super Administrator',
            self::CEO => 'Chief Executive Officer',
            self::COO => 'Chief Operating Officer',
            self::ADMIN => 'Administrator',
            self::DOCTOR => 'Doctor',
            self::NURSE => 'Nurse',
            self::STAFF => 'Staff Member',
            self::PATIENT => 'Patient',
            self::GUEST => 'Guest User',
        };
    }

    /**
     * Get role description.
     */
    public function getDescription(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Full system access with all permissions',
            self::CEO => 'Executive access with strategic oversight and reporting',
            self::COO => 'Operational access with staff management and reporting',
            self::ADMIN => 'Administrative access with most permissions',
            self::DOCTOR => 'Clinical lead with full medical access and patient oversight',
            self::NURSE => 'Clinical support with care coordination and task management',
            self::STAFF => 'Staff-level access with limited permissions',
            self::PATIENT => 'Patient self-service access to personal records and messaging',
            self::GUEST => 'Limited access to public content only',
        };
    }

    /**
     * Check if role is admin level or higher.
     */
    public function isAdminLevel(): bool
    {
        return in_array($this, [self::SUPER_ADMIN, self::CEO, self::COO, self::ADMIN], true);
    }

    /**
     * Check if role is executive level.
     */
    public function isExecutiveLevel(): bool
    {
        return in_array($this, [self::SUPER_ADMIN, self::CEO, self::COO], true);
    }

    /**
     * Get role hierarchy level (higher number = more permissions).
     */
    public function getHierarchyLevel(): int
    {
        return match ($this) {
            self::SUPER_ADMIN => 100,
            self::CEO => 90,
            self::COO => 80,
            self::ADMIN => 70,
            self::DOCTOR => 60,
            self::NURSE => 55,
            self::STAFF => 50,
            self::PATIENT => 30,
            self::GUEST => 10,
        };
    }

    /**
     * Check if this role can manage another role.
     */
    public function canManage(self $otherRole): bool
    {
        return $this->getHierarchyLevel() > $otherRole->getHierarchyLevel();
    }
}
