<?php

namespace App\Services\Validation\Rules;

class EthiopianCalendarDayRules
{
    public static function store(): array
    {
        return [
            'gregorian_date' => 'required|date',
            'ethiopian_date' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'is_holiday' => 'boolean',
            'region' => 'nullable|string|max:100',
        ];
    }

    public static function update(): array
    {
        return [
            'gregorian_date' => 'sometimes|required|date',
            'ethiopian_date' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'is_holiday' => 'boolean',
            'region' => 'nullable|string|max:100',
        ];
    }
}