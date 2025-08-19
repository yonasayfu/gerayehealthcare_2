<?php

namespace App\Http\Controllers\Insurance;

use App\Http\Controllers\Base\BaseController;
use App\Models\Patient;
use App\Models\InsurancePolicy;
use App\Models\EmployeeInsuranceRecord;
use App\Services\Insurance\EmployeeInsuranceRecordService;
use App\Services\Validation\Rules\EmployeeInsuranceRecordRules;
use App\DTOs\CreateEmployeeInsuranceRecordDTO;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\AdditionalExportConfigs;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmployeeInsuranceRecordController extends BaseController
{
    use ExportableTrait;
    public function __construct(EmployeeInsuranceRecordService $employeeInsuranceRecordService)
    {
        parent::__construct(
            $employeeInsuranceRecordService,
            EmployeeInsuranceRecordRules::class,
            'Insurance/EmployeeInsuranceRecords',
            'employeeInsuranceRecords',
            EmployeeInsuranceRecord::class,
            CreateEmployeeInsuranceRecordDTO::class
        );
    }

    public function create()
    {
        $patients = Patient::select('id', 'full_name')->orderBy('full_name')->get();
        $insurancePolicies = InsurancePolicy::select('id', 'service_type')->orderBy('service_type')->get();

        return Inertia::render($this->viewName . '/Create', [
            'patients' => $patients,
            'insurancePolicies' => $insurancePolicies,
        ]);
    }

    public function edit($id)
    {
        $employeeInsuranceRecord = $this->service->getById($id);
        $patients = Patient::select('id', 'full_name')->orderBy('full_name')->get();
        $insurancePolicies = InsurancePolicy::select('id', 'service_type')->orderBy('service_type')->get();

        return Inertia::render($this->viewName . '/Edit', [
            lcfirst(class_basename($this->modelClass)) => $employeeInsuranceRecord,
            'patients' => $patients,
            'insurancePolicies' => $insurancePolicies,
        ]);
    }

    public function show($id)
    {
        // Eager-load relationships needed by the Show.vue (patient, policy)
        $employeeInsuranceRecord = $this->service->getById($id, ['patient', 'policy']);

        // Ensure the prop name matches the frontend's expectation
        return Inertia::render($this->viewName . '/Show', [
            'employeeInsuranceRecord' => $employeeInsuranceRecord,
        ]);
    }

    /**
     * Export Employee Insurance Records (CSV/PDF based on request type).
     */
    public function export(Request $request)
    {
        // Enforce CSV-only export for Employee Insurance Records
        if (strtolower($request->input('type', 'csv')) !== 'csv') {
            abort(404, 'Only CSV export is supported for Employee Insurance Records.');
        }
        $config = $this->buildExportConfig();
        return $this->handleExport($request, EmployeeInsuranceRecord::class, $config);
    }

    /**
     * Print current page/view of Employee Insurance Records.
     */
    public function printCurrent(Request $request)
    {
        abort(404, 'Printing is disabled for Employee Insurance Records.');
    }

    /**
     * Print all Employee Insurance Records.
     */
    public function printAll(Request $request)
    {
        abort(404, 'Printing is disabled for Employee Insurance Records.');
    }

    /**
     * Print a single Employee Insurance Record.
     */
    public function printSingle(Request $request, $id)
    {
        abort(404, 'Printing is disabled for Employee Insurance Records.');
    }

    /**
     * Build export/print configuration using centralized AdditionalExportConfigs.
     */
    private function buildExportConfig(): array
    {
        return AdditionalExportConfigs::getEmployeeInsuranceRecordConfig();
    }
}
