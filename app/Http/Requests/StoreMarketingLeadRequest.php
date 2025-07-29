<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMarketingLeadRequest extends FormRequest
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
}
