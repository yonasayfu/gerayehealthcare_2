<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLandingPageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page_title' => ['required', 'string', 'max:255'],
            'page_url' => ['required', 'url', 'max:255'],
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
}
