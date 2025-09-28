<?php

namespace App\Services\Validation\Rules;

class LeadSourceRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public static function update($item): array
    {
        return self::store();
    }
}
