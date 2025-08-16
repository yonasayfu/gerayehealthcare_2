<?php

namespace App\Services\Validation\Rules;

class LandingPageRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'page_title' => ['required', 'string', 'max:255'],
            'page_url' => ['required', 'url', 'max:255', 'unique:landing_pages,page_url'],
            'template_used' => ['nullable', 'string', 'max:255'],
            'language' => ['string', 'max:10'],
            'form_fields' => ['nullable', 'array'],
            'conversion_goal' => ['nullable', 'string', 'max:255'],
            'views' => ['nullable', 'integer', 'min:0'],
            'submissions' => ['nullable', 'integer', 'min:0'],
            'conversion_rate' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'is_active' => ['boolean'],
            'campaign_id' => ['nullable', 'exists:marketing_campaigns,id'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public static function update($item): array
    {
        $rules = self::store();
        // Adjust unique rule to ignore current record on update
        $rules['page_url'] = ['required', 'url', 'max:255', 'unique:landing_pages,page_url,' . ($item?->id ?? 'NULL')];
        return $rules;
    }
}