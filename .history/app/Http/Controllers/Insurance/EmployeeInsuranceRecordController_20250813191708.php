<?php

namespace App\Http\Controllers\Insurance;

use App\Http\Controllers\Base\BaseController;
use App\Models\Patient;
use App\Models\InsurancePolicy;
use App\Models\EmployeeInsuranceRecord;
use App\Services\Insurance\EmployeeInsuranceRecordService;
use App\Services\Validation\Rules\EmployeeInsuranceRecordRules;
use App\DTOs\CreateEmployeeInsuranceRecordDTO;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmployeeInsuranceRecordController extends BaseController
{
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
}
