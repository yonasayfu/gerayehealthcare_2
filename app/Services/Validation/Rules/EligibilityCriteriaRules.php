<?php

namespace App\Services\Validation\Rules;

class EligibilityCriteriaRules
{
    public static function store(): array
    {
        return [
            'event_id' => 'required|integer|exists:events,id',
            'criteria_title' => 'required|string|max:255',
            'operator' => 'required|string|max:50',
            'value' => 'nullable|string|max:255',
        ];
    }

    public static function update(): array
    {
        return [
            'event_id' => 'sometimes|required|integer|exists:events,id',
            'criteria_title' => 'sometimes|required|string|max:255',
            'operator' => 'sometimes|required|string|max:50',
            'value' => 'nullable|string|max:255',
        ];
    }
}
