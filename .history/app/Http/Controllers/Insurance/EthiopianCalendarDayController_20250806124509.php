<?php

namespace App\Http\Controllers\Insurance;

use App\Http\Controllers\Base\BaseController;
use App\Models\EthiopianCalendarDay;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;
use App\Http\Requests\StoreEthiopianCalendarDayRequest;
use App\Http\Requests\UpdateEthiopianCalendarDayRequest;
use App\Services\Insurance\EthiopianCalendarDayService;
use App\Services\Validation\Rules\EthiopianCalendarDayRules;
use App\DTOs\CreateEthiopianCalendarDayDTO;

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

    public function index(Request $request)
    {
        $data = $this->service->getAll($request);
        
        $data->getCollection()->transform(function ($day) {
            $gregorianDate = new \DateTime($day->gregorian_date);
            $ethiopicDate = \Andegna\DateTimeFactory::fromDateTime($gregorianDate);
            $day->ethiopian_date = $ethiopicDate->format('Y-m-d');
            return $day;
        });

        return Inertia::render('Insurance/EthiopianCalendarDays/Index', [
            'ethiopianCalendarDays' => $data,
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page', 'sort_by', 'sort_order'])
        ]);
    }
