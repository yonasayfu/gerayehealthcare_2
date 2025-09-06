<?php

namespace App\Services\Validation\Rules;

class MarketingLeadRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'lead_code' => ['nullable', 'string', 'max:255'],
            'source_campaign_id' => ['nullable', 'exists:marketing_campaigns,id'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'utm_source' => ['nullable', 'string', 'max:255'],
            'utm_campaign' => ['nullable', 'string', 'max:255'],
            'utm_medium' => ['nullable', 'string', 'max:255'],
            'landing_page_id' => ['nullable', 'exists:landing_pages,id'],
            'lead_score' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'string', 'in:New,Contacted,Qualified,Disqualified,Converted'],
            'assigned_staff_id' => ['nullable', 'exists:staff,id'],
            'converted_patient_id' => ['nullable', 'exists:patients,id'],
            'conversion_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public static function update($item): array
    {
        return self::store();
    }
}
