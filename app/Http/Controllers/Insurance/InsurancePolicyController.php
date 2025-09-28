<?php

namespace App\Http\Controllers\Insurance;

use App\DTOs\CreateInsurancePolicyDTO;
use App\Http\Controllers\Base\BaseController;
use App\Http\Traits\ExportableTrait;
use App\Models\CorporateClient;
use App\Models\InsuranceCompany;
use App\Models\InsurancePolicy;
use App\Services\Insurance\InsurancePolicyService;
use App\Services\Validation\Rules\InsurancePolicyRules;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InsurancePolicyController extends BaseController
{
    use ExportableTrait;

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

        return Inertia::render($this->viewName.'/Create', [
            'insuranceCompanies' => $insuranceCompanies,
            'corporateClients' => $corporateClients,
        ]);
    }

    public function edit($id)
    {
        $insurancePolicy = $this->service->getById($id);
        $insuranceCompanies = InsuranceCompany::select('id', 'name')->orderBy('name')->get();
        $corporateClients = CorporateClient::select('id', 'organization_name')->orderBy('organization_name')->get();

        return Inertia::render($this->viewName.'/Edit', [
            lcfirst(class_basename($this->modelClass)) => $insurancePolicy,
            'insuranceCompanies' => $insuranceCompanies,
            'corporateClients' => $corporateClients,
        ]);
    }

    public function show($id)
    {
        // Eager-load related company and client so Show.vue has the data
        $insurancePolicy = InsurancePolicy::with([
            'insuranceCompany:id,name',
            'corporateClient:id,organization_name',
        ])->findOrFail($id);

        return Inertia::render($this->viewName.'/Show', [
            lcfirst(class_basename($this->modelClass)) => $insurancePolicy,
        ]);
    }

    /**
     * Export all insurance policies (CSV/PDF based on request type).
     * Even if UI hides CSV/PDF, keeping this prevents route errors.
     */
    public function export(Request $request)
    {
        $config = $this->buildExportConfig();

        return $this->handleExport($request, InsurancePolicy::class, $config);
    }

    /**
     * Print current page/view of insurance policies.
     */
    public function printCurrent(Request $request)
    {
        $config = $this->buildExportConfig();

        return $this->handlePrintCurrent($request, InsurancePolicy::class, $config);
    }

    /**
     * Print a single insurance policy.
     */
    public function printSingle(Request $request, $id)
    {
        $model = InsurancePolicy::with(['insuranceCompany:id,name', 'corporateClient:id,organization_name'])->findOrFail($id);
        $config = $this->buildExportConfig();

        return $this->handlePrintSingle($request, $model, $config);
    }

    /**
     * Export/print configuration for Insurance Policies.
     */
    private function buildExportConfig(): array
    {
        $pdfColumns = [
            ['key' => 'insurance_company.name', 'label' => 'Insurance Company'],
            ['key' => 'corporate_client.organization_name', 'label' => 'Corporate Client'],
            ['key' => 'service_type', 'label' => 'Service Type'],
            ['key' => 'coverage_percentage', 'label' => 'Coverage %'],
            ['key' => 'coverage_type', 'label' => 'Coverage Type'],
            ['key' => 'is_active', 'label' => 'Active'],
        ];

        $csvHeaders = ['Insurance Company', 'Corporate Client', 'Service Type', 'Coverage %', 'Coverage Type', 'Active'];
        $csvFields = ['insurance_company.name', 'corporate_client.organization_name', 'service_type', 'coverage_percentage', 'coverage_type', 'is_active'];

        return [
            'csv' => [
                'headers' => $csvHeaders,
                'fields' => $csvFields,
                'filename_prefix' => 'insurance_policies',
            ],
            'pdf' => [
                'view' => 'pdf-layout',
                'document_title' => 'Insurance Policies',
                'filename_prefix' => 'insurance_policies',
                'orientation' => 'landscape',
                'columns' => $pdfColumns,
            ],
            'all_records' => [
                'view' => 'pdf-layout',
                'document_title' => 'Insurance Policies List',
                'filename_prefix' => 'insurance_policies',
                'orientation' => 'landscape',
                'include_index' => true,
                'columns' => $pdfColumns,
            ],
            'current_page' => [
                'view' => 'pdf-layout',
                'document_title' => 'Insurance Policies (Current View)',
                'filename_prefix' => 'insurance_policies_current',
                'orientation' => 'landscape',
                'columns' => $pdfColumns,
            ],
            'single_record' => [
                'view' => 'pdf-layout',
                'document_title' => 'Insurance Policy Details',
                'filename_prefix' => 'insurance_policy',
            ],
        ];
    }
}
