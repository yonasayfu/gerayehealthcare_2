<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMarketingCampaignRequest extends FormRequest
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
}
