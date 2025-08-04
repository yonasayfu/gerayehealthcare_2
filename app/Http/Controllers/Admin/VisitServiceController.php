<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreateVisitServiceDTO;
use App\DTOs\UpdateVisitServiceDTO;
use App\Http\Controllers\Base\BaseController;
use App\Services\VisitServiceService;
use App\Models\VisitService;
use App\Models\Patient;
use App\Models\Staff;
use App\Services\Validation\Rules\VisitServiceRules;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VisitServiceController extends BaseController
{
    public function __construct(VisitServiceService $visitServiceService)
    {
        parent::__construct(
            $visitServiceService,
            VisitServiceRules::class,
            'Admin/VisitServices',
            'visitServices',
            VisitService::class
        );
    }

    

   
}
