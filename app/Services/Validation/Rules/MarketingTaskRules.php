<?php

namespace App\Services\Validation\Rules;

class MarketingTaskRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'campaign_id' => ['required', 'exists:marketing_campaigns,id'],
            'assigned_to_staff_id' => ['required', 'exists:staff,id'],
            'related_content_id' => ['nullable', 'exists:campaign_contents,id'],
            'doctor_id' => ['nullable', 'exists:staff,id'],
            'task_type' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'scheduled_at' => ['required', 'date'],
            'completed_at' => ['nullable', 'date'],
            'status' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public static function update($item): array
    {
        return self::store();
    }
}
