<?php

namespace App\Services\Validation\Rules;

class EligibilityCriteriaRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'service_id' => 'required|exists:services,id',
            'criteria_type' => 'required|string|in:Age,Income,Condition,Other',
            'operator' => 'required|string|in:>,>=,<,<=,=,!=,contains',
            'value' => 'required|string|max:255',
            'description' => 'nullable|string',
        ];
    }
    
    public static function update($criteria): array
    {
        return [
            'service_id' => 'required|exists:services,id',
            'criteria_type' => 'required|string|in:Age,Income,Condition,Other',
            'operator' => 'required|string|in:>,>=,<,<=,=,!=,contains',
            'value' => 'required|string|max:255',
            'description' => 'nullable|string',
        ];
    }
}
