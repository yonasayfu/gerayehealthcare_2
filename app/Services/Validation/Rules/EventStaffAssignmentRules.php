<?php

namespace App\Services\Validation\Rules;

class EventStaffAssignmentRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'event_id' => 'required|exists:events,id',
            'staff_id' => 'required|exists:staff,id',
            'role' => 'required|string|max:255',
        ];
    }

    public static function update($item): array
    {
        return self::store();
    }
}