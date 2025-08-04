<?php

namespace App\Services;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\ExportConfig;

class PatientService extends BaseService
{
    use ExportableTrait;

    public function __construct(Patient $patient)
    {
        parent::__construct($patient);
    }

    protected function applySearch($query, $search)
    {
        $query->where('full_name', 'ilike', "%{$search}%")
              ->orWhere('email', 'ilike', "%{$search}%");
    }

    public function create(array|object $data): Patient
    {
        $data = is_object($data) ? (array) $data : $data; // Ensure $data is an array

        if (Auth::check()) {
            $user = Auth::user();
            $staffMember = \App\Models\Staff::where('user_id', $user->id)->first();

            if ($staffMember) {
                $data['registered_by_staff_id'] = $staffMember->id;
            }
        }

        return parent::create($data);
    }

    public function getById(int $id): Patient
    {
        return $this->model->with(['registeredByStaff'])->findOrFail($id);
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, Patient::class, ExportConfig::getPatientConfig());
    }

    public function printSingle($id)
    {
        $patient = $this->getById($id);
        $patient->load(['registeredByStaff', 'registeredByCaregiver']);

        $config = ExportConfig::getPatientConfig()['single_record'];
        $config['title'] = 'Patient Record - ' . $patient->full_name;
        $config['document_title'] = 'Patient Record';
        $config['filename'] = "patient-{$patient->patient_code}.pdf";
        
        return $this->generateSingleRecordPdf($patient, $config);
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, Patient::class, ExportConfig::getPatientConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, Patient::class, ExportConfig::getPatientConfig());
    }
}
