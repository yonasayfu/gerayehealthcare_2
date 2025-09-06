<?php

namespace App\Services\Validation\Rules;

class MarketingCampaignRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'platform_id' => ['required', 'exists:marketing_platforms,id'],
            'campaign_name' => ['required', 'string', 'max:255'],
            'campaign_type' => ['nullable', 'string', 'max:255'],
            'target_audience' => ['nullable', 'json'],
            'budget_allocated' => ['required', 'numeric', 'min:0'],
            'budget_spent' => ['nullable', 'numeric', 'min:0', 'lte:budget_allocated'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'string', 'max:255'],
            'utm_campaign' => ['nullable', 'string', 'max:255'],
            'utm_source' => ['nullable', 'string', 'max:255'],
            'utm_medium' => ['nullable', 'string', 'max:255'],
            'assigned_staff_id' => ['nullable', 'exists:staff,id'],
            'goals' => ['nullable', 'json'],
        ];
    }

    public static function update($item): array
    {
        return self::store();
    }
}
