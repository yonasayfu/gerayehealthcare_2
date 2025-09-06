<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreatePatientDTO;
use App\Http\Controllers\Base\BaseController;
use App\Http\Traits\ExportableTrait;
use App\Models\CorporateClient;
use App\Models\InsurancePolicy;
use App\Models\Patient;
use App\Services\PatientService;
use App\Services\Validation\Rules\PatientRules;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PatientController extends BaseController
{
    use ExportableTrait;

    public function __construct(PatientService $patientService)
    {
        parent::__construct(
            $patientService,
            PatientRules::class,
            'Admin/Patients',
            'patients',
            Patient::class,
            CreatePatientDTO::class
        );
    }

    public function create()
    {
        $corporateClients = CorporateClient::all();
        $insurancePolicies = InsurancePolicy::with('corporateClient')->get();

        return Inertia::render($this->viewName.'/Create', [
            'corporateClients' => $corporateClients,
            'insurancePolicies' => $insurancePolicies,
        ]);
    }

    public function edit($id)
    {
        $data = $this->service->getById($id);
        $corporateClients = CorporateClient::all();
        $insurancePolicies = InsurancePolicy::with('corporateClient')->get();

        return Inertia::render($this->viewName.'/Edit', [
            lcfirst(class_basename($this->modelClass)) => $data,
            'corporateClients' => $corporateClients,
            'insurancePolicies' => $insurancePolicies,
        ]);
    }

    public function export(Request $request)
    {
        // Use BaseController's export handler
        return $this->handleExport($request, Patient::class, \App\Http\Config\ExportConfig::getPatientConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, Patient::class, \App\Http\Config\ExportConfig::getPatientConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, Patient::class, \App\Http\Config\ExportConfig::getPatientConfig());
    }

    public function printSingle(Request $request, Patient $patient)
    {
        return $this->handlePrintSingle($request, $patient, \App\Http\Config\ExportConfig::getPatientConfig());
    }
}
