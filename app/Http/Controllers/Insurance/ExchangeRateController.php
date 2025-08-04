<?php

namespace App\Http\Controllers\Insurance;

use App\Http\Controllers\Base\BaseController;
use App\Models\ExchangeRate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;
use App\Http\Requests\StoreExchangeRateRequest;
use App\Http\Requests\UpdateExchangeRateRequest;

class ExchangeRateController extends BaseController
{
    public function __construct(ExchangeRateService $exchangeRateService)
    {
        parent::__construct(
            $exchangeRateService,
            ExchangeRateRules::class,
            'Insurance/ExchangeRates',
            'exchangeRates',
            ExchangeRate::class,
            CreateExchangeRateDTO::class
        );
    }

}