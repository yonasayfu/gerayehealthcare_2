<?php

namespace App\Http\Controllers\Insurance;

use App\Http\Controllers\Base\BaseController;
use App\Models\InsuranceClaim;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Mail;
use App\Mail\InsuranceClaimEmail;
use Inertia\Inertia;
use App\Http\Requests\StoreInsuranceClaimRequest;
use App\Http\Requests\UpdateInsuranceClaimRequest;
use App\Http\Requests\SendClaimEmailRequest;

class InsuranceClaimController extends BaseController
{
    public function __construct(InsuranceClaimService $insuranceClaimService)
    {
        parent::__construct(
            $insuranceClaimService,
            InsuranceClaimRules::class,
            'Insurance/Claims',
            'insuranceClaims',
            InsuranceClaim::class,
            CreateInsuranceClaimDTO::class
        );
    }

  
}
