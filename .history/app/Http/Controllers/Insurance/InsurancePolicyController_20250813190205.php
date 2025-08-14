<?php

namespace App\Http\Controllers\Insurance;

use App\Http\Controllers\Base\BaseController;
use App\Models\InsuranceCompany;
use App\Models\CorporateClient;
use App\Services\Validation\Rules\InsurancePolicyRules;
use App\DTOs\CreateInsurancePolicyDTO;
use Illuminate\Http\Request;
use Inertia\Inertia;

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

    public function create()
    {
        $insuranceCompanies = InsuranceCompany::select('id', 'name')->orderBy('name')->get();
        $corporateClients = CorporateClient::select('id', 'organization_name')->orderBy('organization_name')->get();

        return Inertia::render($this->viewName . '/Create', [
            'insuranceCompanies' => $insuranceCompanies,
            'corporateClients' => $corporateClients,
        ]);
    }

    public function edit($id)
    {
        $insurancePolicy = $this->service->getById($id);
        $insuranceCompanies = InsuranceCompany::select('id', 'name')->orderBy('name')->get();
        $corporateClients = CorporateClient::select('id', 'organization_name')->orderBy('organization_name')->get();

        return Inertia::render($this->viewName . '/Edit', [
            lcfirst(class_basename($this->modelClass)) => $insurancePolicy,
            'insuranceCompanies' => $insuranceCompanies,
            'corporateClients' => $corporateClients,
        ]);
    }
}
