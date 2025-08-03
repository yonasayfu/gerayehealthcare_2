<?php

namespace App\Services\Validation\Rules;

class EventBroadcastRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'event_id' => 'required|exists:events,id',
            'channel' => 'required|string|max:255',
            'message' => 'required|string',
            'sent_by_staff_id' => 'required|exists:staff,id',
        ];
    }

    public static function update($item): array
    {
        return self::store();
    }
}