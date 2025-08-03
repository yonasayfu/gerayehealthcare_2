<?php

namespace App\Services\Validation\Rules;

class MarketingAnalyticsRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'campaign_id' => 'required|exists:marketing_campaigns,id',
            'platform_id' => 'required|exists:marketing_platforms,id',
            'date' => 'required|date',
            'impressions' => 'required|integer|min:0',
            'clicks' => 'required|integer|min:0',
            'conversions' => 'required|integer|min:0',
            'spend' => 'required|numeric|min:0',
        ];
    }
    
    public static function update($analytics): array
    {
        return [
            'campaign_id' => 'required|exists:marketing_campaigns,id',
            'platform_id' => 'required|exists:marketing_platforms,id',
            'date' => 'required|date',
            'impressions' => 'required|integer|min:0',
            'clicks' => 'required|integer|min:0',
            'conversions' => 'required|integer|min:0',
            'spend' => 'required|numeric|min:0',
        ];
    }
}
