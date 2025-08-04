<?php

namespace App\Services\Validation\Rules;

class EligibilityCriteriaRules
{
    public static function store(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'criteria_type' => 'nullable|string|max:255',
            'value' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ];
    }

    public static function update(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'criteria_type' => 'nullable|string|max:255',
            'value' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ];
    }
}