<?php

namespace App\Services\Validation\Rules;

class EthiopianCalendarDayRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'ethiopian_date' => 'required|date|unique:ethiopian_calendar_days,ethiopian_date',
            'gregorian_date' => 'required|date|unique:ethiopian_calendar_days,gregorian_date',
            'is_holiday' => 'boolean',
            'holiday_name' => 'nullable|string|max:255',
            'holiday_type' => 'nullable|string|in:National,Religious,Other',
        ];
    }
    
    public static function update($day): array
    {
        return [
            'ethiopian_date' => 'required|date|unique:ethiopian_calendar_days,ethiopian_date,' . $day->id,
            'gregorian_date' => 'required|date|unique:ethiopian_calendar_days,gregorian_date,' . $day->id,
            'is_holiday' => 'boolean',
            'holiday_name' => 'nullable|string|max:255',
            'holiday_type' => 'nullable|string|in:National,Religious,Other',
        ];
    }
}
