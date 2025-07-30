<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMarketingBudgetRequest extends FormRequest
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
            'budget_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'allocated_amount' => ['required', 'numeric', 'min:0'],
            'spent_amount' => ['nullable', 'numeric', 'min:0'],
            'period_start' => ['required', 'date'],
            'period_end' => ['nullable', 'date', 'after_or_equal:period_start'],
            'status' => ['required', 'string', 'in:Planned,Active,Completed,On Hold,Cancelled'],
        ];
    }
}