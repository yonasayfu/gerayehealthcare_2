<?php

namespace App\Services\Validation\Rules;

class CampaignContentRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'campaign_id' => ['required', 'exists:marketing_campaigns,id'],
            'platform_id' => ['required', 'exists:marketing_platforms,id'],
            'content_type' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'media_url' => ['nullable', 'url', 'max:255'],
            'scheduled_post_date' => ['required', 'date'],
            'actual_post_date' => ['nullable', 'date'],
            'status' => ['required', 'string', 'max:255'],
            'engagement_metrics' => ['nullable', 'json'],
        ];
    }

    public static function update($item): array
    {
        return self::store();
    }
}
