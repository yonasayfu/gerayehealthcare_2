<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCampaignContentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
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
}
