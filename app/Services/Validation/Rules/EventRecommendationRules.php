<?php

namespace App\Services\Validation\Rules;

class EventRecommendationRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'event_id' => 'required|exists:events,id',
            'source_channel' => 'required|string|max:255',
            'recommended_by_name' => 'nullable|string|max:255',
            'recommended_by_phone' => 'nullable|string|max:255',
            'patient_name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|string|in:pending,approved,rejected',
        ];
    }

    public static function update($item): array
    {
        return self::store();
    }
}
