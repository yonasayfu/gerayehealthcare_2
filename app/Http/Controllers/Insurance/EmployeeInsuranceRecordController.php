<?php

namespace App\Http\Controllers\Insurance;

use App\Http\Controllers\Controller;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\AdditionalExportConfigs;
use App\Models\EmployeeInsuranceRecord;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;

class EmployeeInsuranceRecordController extends Controller
{
    use ExportableTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = EmployeeInsuranceRecord::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('kebele_id', 'ilike', "%{$search}%")
                  ->orWhere('employee_id_number', 'ilike', "%{$search}%");
        }

        if ($request->filled('sort') && !empty($request->input('sort'))) {
            $query->orderBy($request->input('sort'), $request->input('direction', 'asc'));
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $employeeInsuranceRecords = $query->paginate($request->input('per_page', 5))->withQueryString();

        return Inertia::render('Insurance/EmployeeInsuranceRecords/Index', [
            'employeeInsuranceRecords' => $employeeInsuranceRecords,
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Insurance/EmployeeInsuranceRecords/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'policy_id' => 'required|exists:insurance_policies,id',
            'kebele_id' => 'nullable|string|max:255',
            'woreda' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'federal_id' => 'nullable|string|max:255',
            'ministry_department' => 'nullable|string|max:255',
            'employee_id_number' => 'nullable|string|max:255',
            'verified' => 'required|boolean',
            'verified_at' => 'nullable|date',
        ]);

        EmployeeInsuranceRecord::create($validated);

        return Redirect::route('admin.employee-insurance-records.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $employeeInsuranceRecord = EmployeeInsuranceRecord::findOrFail($id);
        return Inertia::render('Insurance/EmployeeInsuranceRecords/Show', [
            'employeeInsuranceRecord' => $employeeInsuranceRecord,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $employeeInsuranceRecord = EmployeeInsuranceRecord::findOrFail($id);
        return Inertia::render('Insurance/EmployeeInsuranceRecords/Edit', [
            'employeeInsuranceRecord' => $employeeInsuranceRecord,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $employeeInsuranceRecord = EmployeeInsuranceRecord::findOrFail($id);

        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'policy_id' => 'required|exists:insurance_policies,id',
            'kebele_id' => 'nullable|string|max:255',
            'woreda' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'federal_id' => 'nullable|string|max:255',
            'ministry_department' => 'nullable|string|max:255',
            'employee_id_number' => 'nullable|string|max:255',
            'verified' => 'required|boolean',
            'verified_at' => 'nullable|date',
        ]);

        $employeeInsuranceRecord->update($validated);

        return Redirect::route('admin.employee-insurance-records.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $employeeInsuranceRecord = EmployeeInsuranceRecord::findOrFail($id);

        $employeeInsuranceRecord->delete();

        return Redirect::route('admin.employee-insurance-records.index');
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, EmployeeInsuranceRecord::class, AdditionalExportConfigs::getEmployeeInsuranceRecordConfig());
    }

    public function printSingle(EmployeeInsuranceRecord $employeeInsuranceRecord)
    {
        return $this->handlePrintSingle($employeeInsuranceRecord, AdditionalExportConfigs::getEmployeeInsuranceRecordConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, EmployeeInsuranceRecord::class, AdditionalExportConfigs::getEmployeeInsuranceRecordConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, EmployeeInsuranceRecord::class, AdditionalExportConfigs::getEmployeeInsuranceRecordConfig());
    }
}
