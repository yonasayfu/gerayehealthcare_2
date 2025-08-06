<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Models\EligibilityCriteria;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use App\Http\Requests\StoreEligibilityCriteriaRequest;
use App\Http\Requests\UpdateEligibilityCriteriaRequest;
use App\Services\EligibilityCriteriaService;

class EligibilityCriteriaController extends BaseController
{
    public function __construct(EligibilityCriteriaService $eligibilityCriteriaService)
    {
        parent::__construct(
            $eligibilityCriteriaService,
            EligibilityCriteriaRules::class,
            'Admin/EligibilityCriteria',
            'eligibilityCriteria',
            EligibilityCriteria::class,
            CreateEligibilityCriteriaDTO::class
        );
        $this->middleware('role:' . \App\Enums\RoleEnum::SUPER_ADMIN->value . '|' . \App\Enums\RoleEnum::ADMIN->value);
    }

}
