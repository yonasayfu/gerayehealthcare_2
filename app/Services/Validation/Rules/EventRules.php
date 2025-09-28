<?php

namespace App\Services\Validation\Rules;

class EventRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'is_free_service' => 'boolean',
            'broadcast_status' => 'required|string|in:Draft,Published,Archived',
        ];
    }

    public static function update($event): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'is_free_service' => 'boolean',
            'broadcast_status' => 'required|string|in:Draft,Published,Archived',
        ];
    }
}
