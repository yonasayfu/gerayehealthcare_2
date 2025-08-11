<?php

namespace App\Services;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\ExportConfig;
use App\Services\Insurance\EmployeeInsuranceRecordService;
use App\Models\Staff;

class PatientService extends BaseService
{
    use ExportableTrait;

    protected $employeeInsuranceRecordService;

    public function __construct(Patient $patient, EmployeeInsuranceRecordService $employeeInsuranceRecordService)
    {
        parent::__construct($patient);
        $this->employeeInsuranceRecordService = $employeeInsuranceRecordService;
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
            $staffMember = Staff::where('user_id', $user->id)->first();

            if (!$staffMember) {
                // Auto-provision a minimal Staff record for non-staff users (e.g., Super Admin) to track registrars
                $fullName = trim((string)($user->name ?? ''));
                $parts = preg_split('/\s+/', $fullName, -1, PREG_SPLIT_NO_EMPTY) ?: [];
                $firstName = $parts[0] ?? ($user->first_name ?? 'Admin');
                $lastName = $parts[1] ?? ($user->last_name ?? 'User');
                $staffMember = Staff::create([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $user->email ?? null,
                    'user_id' => $user->id,
                    'status' => 'Active',
                ]);
            }

            if ($staffMember) {
                $data['registered_by_staff_id'] = $staffMember->id;
            }
        }

        $patient = parent::create($data);

        if (isset($data['corporate_client_id']) && isset($data['policy_id'])) {
            $this->employeeInsuranceRecordService->create([
                'patient_id' => $patient->id,
                'policy_id' => $data['policy_id'],
                // Add other fields for employee_insurance_records if necessary, e.g., kebele_id, woreda, etc.
            ]);
        }

        return $patient;
    }

    public function update(int $id, array|object $data): Patient
    {
        $data = is_object($data) ? (array) $data : $data;

        $patient = parent::update($id, $data);

        if (isset($data['corporate_client_id']) && isset($data['policy_id'])) {
            // Check if an existing record needs to be updated or a new one created
            $existingRecord = $patient->employeeInsuranceRecords()
                                      ->where('policy_id', $data['policy_id'])
                                      ->first();

            if ($existingRecord) {
                $this->employeeInsuranceRecordService->update($existingRecord->id, [
                    'policy_id' => $data['policy_id'],
                    // Update other fields if necessary
                ]);
            } else {
                $this->employeeInsuranceRecordService->create([
                    'patient_id' => $patient->id,
                    'policy_id' => $data['policy_id'],
                    // Add other fields for employee_insurance_records if necessary
                ]);
            }
        }

        return $patient;
    }

    public function getById(int $id): Patient
    {
        return $this->model->with([
            'registeredByStaff',
            'employeeInsuranceRecords.policy.corporateClient',
        ])->findOrFail($id);
    }

    public function export(Request $request)
    {
        // Add debug logging
        \Log::info('PatientService export called', [
            'request_type' => $request->input('type'),
            'request_params' => $request->all()
        ]);
        
        $result = $this->handleExport($request, Patient::class, ExportConfig::getPatientConfig());
        
        // Add debug logging for result
        \Log::info('PatientService export result', [
            'result_type' => gettype($result),
            'result_class' => get_class($result)
        ]);
        
        return $result;
    }

    public function printSingle($id, Request $request)
    {
        $patient = $this->getById($id);
        $patient->load(['registeredByStaff', 'registeredByCaregiver']);

        $config = ExportConfig::getPatientConfig();
        
        return $this->handlePrintSingle($request, $patient, $config);
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
