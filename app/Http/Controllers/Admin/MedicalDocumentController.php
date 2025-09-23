<?php

namespace App\Http\Controllers\Admin;

use App\Http\Config\AdditionalExportConfigs;
use App\Http\Controllers\Base\BaseController;
use App\Models\MedicalDocument;
use App\Models\MedicalVisit;
use App\Models\Patient;
use App\Services\MedicalDocument\MedicalDocumentService;
use App\Services\Validation\Rules\MedicalDocumentRules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MedicalDocumentController extends BaseController
{
    public function __construct(MedicalDocumentService $service)
    {
        parent::__construct(
            $service,
            MedicalDocumentRules::class,
            'Admin/MedicalDocuments',
            'medicalDocuments',
            MedicalDocument::class
        );
    }

    public function create()
    {
        $patients = Patient::select('id', 'full_name', 'patient_code')->orderBy('full_name')->get();
        return inertia('Admin/MedicalDocuments/Create', [
            'patients' => $patients,
        ]);
    }

    public function store(Request $request)
    {
        // Ensure created_by_staff_id is set before validation
        if (!$request->filled('created_by_staff_id') && Auth::check() && Auth::user()->staff) {
            $request->merge(['created_by_staff_id' => Auth::user()->staff->id]);
        }
        return parent::store($request);
    }

    public function update(Request $request, $id)
    {
        // Ensure created_by_staff_id is preserved during update
        $medicalDocument = $this->service->getById($id);
        if (!$request->filled('created_by_staff_id') && $medicalDocument->created_by_staff_id) {
            $request->merge(['created_by_staff_id' => $medicalDocument->created_by_staff_id]);
        }
        return parent::update($request, $id);
    }

    /**
     * Test method to return recent medical visits for a given patient ID (for selector population).
     */
    public function visitsForPatient($patientId)
    {
        Log::info('visitsForPatient called with patient ID: ' . $patientId);

        $visits = MedicalVisit::where('patient_id', $patientId)
            ->orderByDesc('visit_date')
            ->select('id', 'visit_date', 'visit_type')
            ->limit(100)
            ->get();

        Log::info('Found ' . $visits->count() . ' visits for patient ID ' . $patientId);

        return response()->json(['visits' => $visits]);
    }

    public function export(Request $request)
    {
        $config = AdditionalExportConfigs::getMedicalDocumentConfig();

        return $this->handleExport($request, MedicalDocument::class, $config);
    }

    public function printAll(Request $request)
    {
        $config = AdditionalExportConfigs::getMedicalDocumentConfig();

        return $this->handlePrintAll($request, MedicalDocument::class, $config);
    }

    public function printCurrent(Request $request)
    {
        $config = AdditionalExportConfigs::getMedicalDocumentConfig();

        return $this->handlePrintCurrent($request, MedicalDocument::class, $config);
    }

    public function printSingle(Request $request, $id)
    {
        $model = MedicalDocument::findOrFail($id);
        $config = AdditionalExportConfigs::getMedicalDocumentConfig();

        return $this->handlePrintSingle($request, $model, $config);
    }

    public function edit($id)
    {
        $medicalDocument = MedicalDocument::with(['patient', 'visit'])->findOrFail($id);
        $patients = Patient::select('id', 'full_name', 'patient_code')->orderBy('full_name')->get();

        return inertia('Admin/MedicalDocuments/Edit', [
            'medicalDocument' => $medicalDocument,
            'patients' => $patients,
        ]);
    }
}
