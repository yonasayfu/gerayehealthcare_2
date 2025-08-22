<?php

namespace App\Services\Validation\Rules;

class PartnerEngagementRules
{
    public static function store(): array
    {
        return [
            'partner_id' => ['required', 'integer', 'exists:partners,id'],
            'staff_id' => ['required', 'integer', 'exists:staff,id'],
            'engagement_type' => ['required', 'string', 'max:255'],
            'summary' => ['required', 'string'],
            'engagement_date' => ['required', 'date'],
            'follow_up_date' => ['nullable', 'date'],
        ];
    }

    public static function update($model): array
    {
        return [
            'partner_id' => ['sometimes', 'integer', 'exists:partners,id'],
            'staff_id' => ['sometimes', 'integer', 'exists:staff,id'],
            'engagement_type' => ['sometimes', 'string', 'max:255'],
            'summary' => ['sometimes', 'string'],
            'engagement_date' => ['sometimes', 'date'],
            'follow_up_date' => ['nullable', 'date'],
        ];
    }
}
