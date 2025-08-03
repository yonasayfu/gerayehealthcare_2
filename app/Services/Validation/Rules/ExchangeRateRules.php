<?php

namespace App\Services\Validation\Rules;

class ExchangeRateRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'currency_from' => 'required|string|size:3',
            'currency_to' => 'required|string|size:3',
            'rate' => 'required|numeric|min:0',
            'effective_date' => 'required|date',
            'is_active' => 'boolean',
        ];
    }
    
    public static function update($rate): array
    {
        return [
            'currency_from' => 'required|string|size:3',
            'currency_to' => 'required|string|size:3',
            'rate' => 'required|numeric|min:0',
            'effective_date' => 'required|date',
            'is_active' => 'boolean',
        ];
    }
}
