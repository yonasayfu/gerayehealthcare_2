<?php

namespace App\Http\Controllers\Insurance;

use App\DTOs\CreateInsuranceCompanyDTO;
use App\DTOs\UpdateInsuranceCompanyDTO;
use App\Http\Controllers\Base\BaseController;
use App\Http\Traits\ExportableTrait; // Corrected namespace
use App\Models\InsuranceCompany;
use App\Services\Insurance\InsuranceCompanyService;
use App\Services\Validation\Rules\InsuranceCompanyRules;
use Illuminate\Http\Request;

class InsuranceCompanyController extends BaseController
{
    use ExportableTrait;

    public function __construct(InsuranceCompanyService $insuranceCompanyService)
    {
        parent::__construct(
            $insuranceCompanyService,
            InsuranceCompanyRules::class,
            'Insurance/Companies',
            'insuranceCompanies',
            InsuranceCompany::class,
            CreateInsuranceCompanyDTO::class,
            UpdateInsuranceCompanyDTO::class
        );
    }

    /**
     * Export all insurance companies as CSV or PDF depending on request type.
     */
    public function export(Request $request)
    {
        $config = $this->buildExportConfig();

        return $this->handleExport($request, InsuranceCompany::class, $config);
    }

    /**
     * Print all insurance companies (PDF) with current filters applied.
     */
    public function printAll(Request $request)
    {
        $config = $this->buildExportConfig();

        return $this->handlePrintAll($request, InsuranceCompany::class, $config);
    }

    /**
     * Print current page/view (PDF with pagination and current filters).
     */
    public function printCurrent(Request $request)
    {
        $config = $this->buildExportConfig();

        return $this->handlePrintCurrent($request, InsuranceCompany::class, $config);
    }

    /**
     * Print a single insurance company (PDF).
     */
    public function printSingle(Request $request, $id)
    {
        $model = InsuranceCompany::findOrFail($id);
        $config = $this->buildExportConfig();

        return $this->handlePrintSingle($request, $model, $config);
    }

    /**
     * Export/print configuration for Insurance Companies.
     */
    private function buildExportConfig(): array
    {
        $pdfColumns = [
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'contact_person', 'label' => 'Contact Person'],
            ['key' => 'contact_email', 'label' => 'Contact Email'],
            ['key' => 'contact_phone', 'label' => 'Contact Phone'],
            ['key' => 'address', 'label' => 'Address'],
        ];

        $csvHeaders = ['Name', 'Contact Person', 'Contact Email', 'Contact Phone', 'Address'];
        $csvFieldsMap = [
            'Name' => 'name',
            'Contact Person' => 'contact_person',
            'Contact Email' => 'contact_email',
            'Contact Phone' => 'contact_phone',
            'Address' => 'address',
        ];
        $csvFields = [];
        foreach ($csvHeaders as $label) {
            $csvFields[] = $csvFieldsMap[$label] ?? '';
        }

        return [
            'csv' => [
                'headers' => $csvHeaders,
                'fields' => $csvFields,
                'filename_prefix' => 'insurance_companies',
            ],
            'pdf' => [
                'view' => 'pdf-layout',
                'document_title' => 'Insurance Companies',
                'filename_prefix' => 'insurance_companies',
                'orientation' => 'landscape',
                'columns' => $pdfColumns,
            ],
            'all_records' => [
                'view' => 'pdf-layout',
                'document_title' => 'Insurance Companies List',
                'filename_prefix' => 'insurance_companies',
                'orientation' => 'landscape',
                'include_index' => true,
                'columns' => $pdfColumns,
            ],
            'current_page' => [
                'view' => 'pdf-layout',
                'document_title' => 'Insurance Companies (Current View)',
                'filename_prefix' => 'insurance_companies_current',
                'orientation' => 'landscape',
                'columns' => $pdfColumns,
            ],
            'single_record' => [
                'view' => 'pdf-layout',
                'document_title' => 'Insurance Company Details',
                'filename_prefix' => 'insurance_company',
            ],
        ];
    }
}
