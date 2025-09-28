<?php

namespace App\Services\Validation\Rules;

use Illuminate\Foundation\Http\FormRequest;

class GetCalendarEventsRules extends FormRequest
{
    public function rules(): array
    {
        return [
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
