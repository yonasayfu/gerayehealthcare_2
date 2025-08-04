<?php

namespace App\Services\Validation\Rules;

class ExchangeRateRules
{
    public static function store(): array
    {
        return [
            'currency_code' => 'required|string|max:10',
            'rate_to_etb' => 'required|numeric',
            'source' => 'nullable|string|max:255',
            'date_effective' => 'required|date',
        ];
    }

    public static function update(): array
    {
        return [
            'currency_code' => 'sometimes|required|string|max:10',
            'rate_to_etb' => 'sometimes|required|numeric',
            'source' => 'nullable|string|max:255',
            'date_effective' => 'sometimes|required|date',
        ];
    }
}