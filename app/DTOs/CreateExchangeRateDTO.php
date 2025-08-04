<?php

namespace App\DTOs;

class CreateExchangeRateDTO
{
    public function __construct(
        public string $currency_code,
        public float $rate_to_etb,
        public ?string $source,
        public string $date_effective
    ) {}
}
