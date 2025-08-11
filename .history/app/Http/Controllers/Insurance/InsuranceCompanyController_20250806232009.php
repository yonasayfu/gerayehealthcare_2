<?php

namespace App\Http\Controllers\Insurance;

use App\Services\Validation\Rules\InsuranceCompanyRules;
use App\Http\Controllers\Base\BaseController;
use App\Models\InsuranceCompany;
use App\Services\InsuranceCompanyService;
use App\DTOs\CreateInsuranceCompanyDTO;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Requests\StoreInsuranceCompanyRequest;
use App\Http\Requests\UpdateInsuranceCompanyRequest;

class InsuranceCompanyController extends BaseController
{
    public function __construct(InsuranceCompanyService $insuranceCompanyService)
    {
        parent::__construct(
            $insuranceCompanyService,
            InsuranceCompanyRules::class,
            'Insurance/Companies',
            'insuranceCompanies',
            InsuranceCompany::class,
            CreateInsuranceCompanyDTO::class
        );
    }

 }
