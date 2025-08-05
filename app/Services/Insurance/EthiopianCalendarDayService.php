<?php

namespace App\Services\Insurance;

use App\Models\EthiopianCalendarDay;
use App\DTOs\CreateEthiopianCalendarDayDTO;
use App\Services\BaseService;

class EthiopianCalendarDayService extends BaseService
{
    public function __construct(EthiopianCalendarDay $ethiopianCalendarDay)
    {
        parent::__construct($ethiopianCalendarDay);
    }

    public function create(array|object $data): EthiopianCalendarDay
    {
        $data = is_object($data) ? (array) $data : $data;
        return parent::create($data);
    }
}
