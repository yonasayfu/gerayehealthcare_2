<?php

namespace App\Http\Controllers\Admin;

use App\Http\Config\AdditionalExportConfigs;
use App\Http\Controllers\Base\BaseController;
use App\Http\Traits\ExportableTrait;
use App\Models\MedicalDocument;
use App\Models\Patient;
use App\Models\MedicalVisit;
use App\Services\MedicalDocumentService;
use App\Services\Validation\Rules\MedicalDocumentRules;
use Illuminate\Http\Request;

class MedicalDocumentController extends BaseController
{
    use ExportableTrait;

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
        $patients = Patient::select('id','full_name','patient_code')->orderBy('full_name')->get();
        return inertia('Admin/MedicalDocuments/Create', [
            'patients' => $patients,
        ]);
    }

    public function store(Request $request)
    {
        if (! $request->filled('created_by_staff_id') && optional(auth()->user())->staff) {
            $request->merge(['created_by_staff_id' => auth()->user()->staff->id]);
        }
        return parent::store($request);
    }

    /**
     * Return recent medical visits for a given patient (for selector population).
     */
    public function visitsForPatient(Patient $patient)
    {
        $visits = MedicalVisit::where('patient_id', $patient->id)
            ->orderByDesc('visit_date')
            ->select('id', 'visit_date', 'visit_type')
            ->limit(100)
            ->get();

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
}
