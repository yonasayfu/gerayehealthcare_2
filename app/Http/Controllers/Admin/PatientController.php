<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\PatientService;
use App\Models\Patient;
use App\Models\CorporateClient;
use App\Models\InsurancePolicy;
use App\Services\Validation\Rules\PatientRules;
use App\DTOs\CreatePatientDTO;
use Inertia\Inertia;
use Illuminate\Http\Request;

class PatientController extends BaseController
{
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

    public function printAll(Request $request)
    {
        // Use the export/print pipeline to generate a PDF preview in browser
        return $this->service->printAll($request);
    }

    public function printCurrent(Request $request)
    {
        // Keep existing behavior (if any) but index page will trigger in-page print without new tab
        return Inertia::render($this->viewName . '/Index', [
            $this->dataVariableName => $this->service->getAll($request),
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page', 'sort_by', 'sort_order'])
        ]);
    }
    public function create()
    {
        $corporateClients = CorporateClient::all();
        $insurancePolicies = InsurancePolicy::with('corporateClient')->get();
        return Inertia::render($this->viewName . '/Create', [
            'corporateClients' => $corporateClients,
            'insurancePolicies' => $insurancePolicies,
        ]);
    }

    public function edit($id)
    {
        $data = $this->service->getById($id);
        $corporateClients = CorporateClient::all();
        $insurancePolicies = InsurancePolicy::with('corporateClient')->get();
        return Inertia::render($this->viewName . '/Edit', [
            lcfirst(class_basename($this->modelClass)) => $data,
            'corporateClients' => $corporateClients,
            'insurancePolicies' => $insurancePolicies,
        ]);
    }
}
