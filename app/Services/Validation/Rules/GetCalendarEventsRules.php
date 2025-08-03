<?php

namespace App\Services\Validation\Rules;

use Illuminate\Foundation\Http\FormRequest;

class GetCalendarEventsRules extends FormRequest
{
    public static function get(): array
    {
        return [
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
        ];
    }
}
