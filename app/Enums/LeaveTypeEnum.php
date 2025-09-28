<?php

namespace App\Enums;

enum LeaveTypeEnum: string
{
    case ANNUAL = 'Annual';
    case SICK = 'Sick';
    case UNPAID = 'Unpaid';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

