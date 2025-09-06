<?php

namespace App\Services\Validation\Rules;

class EventParticipantRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'event_id' => 'required|exists:events,id',
            'patient_id' => 'required|exists:patients,id',
            'status' => 'required|string|in:registered,attended,no-show',
        ];
    }

    public static function update($item): array
    {
        return self::store();
    }
}
