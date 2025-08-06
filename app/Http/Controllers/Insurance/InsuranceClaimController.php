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
use App\Services\Insurance\InsuranceClaimService;
use App\Models\Patient;
use App\Models\Invoice;
use App\Models\InsuranceCompany;
use App\Models\InsurancePolicy;

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

    public function create()
    {
        $patients = Patient::all(['id', 'full_name']);
        $invoices = Invoice::all(['id', 'invoice_number', 'grand_total']);
        $insuranceCompanies = InsuranceCompany::all(['id', 'name']);
        $insurancePolicies = InsurancePolicy::all(['id', 'service_type', 'coverage_percentage']);

        return Inertia::render($this->viewName . '/Create', [
            'patients' => $patients,
            'invoices' => $invoices,
            'insuranceCompanies' => $insuranceCompanies,
            'insurancePolicies' => $insurancePolicies,
        ]);
    }

    public function edit($id)
    {
        $insuranceClaim = $this->service->getById($id);
        $patients = Patient::all(['id', 'full_name']);
        $invoices = Invoice::all(['id', 'invoice_number', 'grand_total']);
        $insuranceCompanies = InsuranceCompany::all(['id', 'name']);
        $insurancePolicies = InsurancePolicy::all(['id', 'service_type', 'coverage_percentage']);

        return Inertia::render($this->viewName . '/Edit', [
            'insuranceClaim' => $insuranceClaim,
            'patients' => $patients,
            'invoices' => $invoices,
            'insuranceCompanies' => $insuranceCompanies,
            'insurancePolicies' => $insurancePolicies,
        ]);
    }
}
