<?php

namespace App\Http\Controllers\Insurance;

use App\Http\Controllers\Base\BaseController;
use App\Models\InsurancePolicy;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;
use App\Http\Requests\StoreInsurancePolicyRequest;
use App\Http\Requests\UpdateInsurancePolicyRequest;
use App\Services\Insurance\InsurancePolicyService;

class InsurancePolicyController extends BaseController
{
    public function __construct(InsurancePolicyService $insurancePolicyService)
    {
        parent::__construct(
            $insurancePolicyService,
            InsurancePolicyRules::class,
            'Insurance/Policies',
            'insurancePolicies',
            InsurancePolicy::class,
            CreateInsurancePolicyDTO::class
        );
    }

 }