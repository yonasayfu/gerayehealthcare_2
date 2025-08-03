<?php

namespace App\Services\Validation\Rules;

use Illuminate\Validation\Rule;

class MarketingPlatformRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('marketing_platforms', 'name')],
            'api_endpoint' => ['nullable', 'url', 'max:255'],
            'api_credentials' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }

    public static function update($item): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('marketing_platforms', 'name')->ignore($item->id)],
            'api_endpoint' => ['nullable', 'url', 'max:255'],
            'api_credentials' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }
}