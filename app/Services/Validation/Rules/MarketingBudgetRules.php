<?php

namespace App\Services\Validation\Rules;

class MarketingBudgetRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'campaign_id' => ['required', 'exists:marketing_campaigns,id'],
            'platform_id' => ['required', 'exists:marketing_platforms,id'],
            'budget_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'allocated_amount' => ['required', 'numeric', 'min:0'],
            'spent_amount' => ['nullable', 'numeric', 'min:0'],
            'period_start' => ['required', 'date'],
            'period_end' => ['nullable', 'date', 'after_or_equal:period_start'],
            'status' => ['required', 'string', 'in:Planned,Active,Completed,On Hold,Cancelled'],
        ];
    }

    public static function update($item): array
    {
        return self::store();
    }
}
