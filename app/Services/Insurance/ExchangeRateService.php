<?php

namespace App\Services\Insurance;

use App\Models\ExchangeRate;
use App\DTOs\CreateExchangeRateDTO;
use App\Services\BaseService;

class ExchangeRateService extends BaseService
{
    public function __construct(ExchangeRate $exchangeRate)
    {
        parent::__construct($exchangeRate);
    }

    public function create(array $data): ExchangeRate
    {
        return parent::create($data);
    }
}
