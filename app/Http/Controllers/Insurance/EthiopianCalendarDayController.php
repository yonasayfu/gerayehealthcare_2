<?php

namespace App\Http\Controllers\Insurance;

use App\DTOs\CreateEthiopianCalendarDayDTO;
use App\Http\Controllers\Base\BaseController;
use App\Models\EthiopianCalendarDay;
use App\Services\Insurance\EthiopianCalendarDayService;
use App\Services\Validation\Rules\EthiopianCalendarDayRules;

class EthiopianCalendarDayController extends BaseController
{
    public function __construct(EthiopianCalendarDayService $ethiopianCalendarDayService)
    {
        parent::__construct(
            $ethiopianCalendarDayService,
            EthiopianCalendarDayRules::class,
            'Insurance/EthiopianCalendarDays',
            'ethiopianCalendarDays',
            EthiopianCalendarDay::class,
            CreateEthiopianCalendarDayDTO::class
        );
    }
}
