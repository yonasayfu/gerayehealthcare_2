<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreatePatientDTO;
use App\Http\Controllers\Base\OptimizedBaseController;
use App\Models\Patient;
use App\Services\OptimizedPatientService;
use App\Services\Validation\Rules\PatientRules;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OptimizedPatientController extends OptimizedBaseController
{
    protected $patientService;

    // Define eager loading relationships to prevent N+1 queries
    protected $indexWith = ['corporateClient', 'insurancePolicy'];

    protected $showWith = ['corporateClient', 'insurancePolicy', 'visitServices'];

    protected $editWith = ['corporateClient', 'insurancePolicy'];

    public function __construct(OptimizedPatientService $patientService)
    {
        $this->patientService = $patientService;

        parent::__construct(
            $patientService,
            PatientRules::class,
            'Admin/Patients',
            'patients',
            Patient::class,
            CreatePatientDTO::class
        );
    }

    public function index(Request $request)
    {
        // Use optimized service with caching and eager loading
        $patients = $this->patientService->getAll($request, $this->indexWith);

        // Get cached statistics for the dashboard info
        $statistics = $this->patientService->getStatistics();

        return Inertia::render($this->viewName.'/Index', [
            'patients' => $patients,
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page']),
            'statistics' => $statistics, // Additional dashboard data
        ]);
    }

    public function show($id)
    {
        // Use optimized service with eager loading
        $patient = $this->patientService->getById($id, $this->showWith);

        return Inertia::render($this->viewName.'/Show', [
            'patient' => $patient,
        ]);
    }

    public function create()
    {
        // Use cached form data
        $formData = $this->patientService->getFormData();

        return Inertia::render($this->viewName.'/Create', $formData);
    }

    public function edit($id)
    {
        // Use optimized service with eager loading
        $patient = $this->patientService->getById($id, $this->editWith);
        $formData = $this->patientService->getFormData();

        return Inertia::render($this->viewName.'/Edit', array_merge([
            'patient' => $patient,
        ], $formData));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate(PatientRules::create());

        // Use DTO for data transfer
        $dto = CreatePatientDTO::from($validatedData);
        $patient = $this->patientService->create($dto);

        return redirect()->route('admin.patients.index')
            ->with('banner', 'Patient created successfully!')
            ->with('bannerStyle', 'success');
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate(PatientRules::update());

        // Use DTO for data transfer
        $dto = CreatePatientDTO::from($validatedData);
        $patient = $this->patientService->update($id, $dto);

        return redirect()->route('admin.patients.index')
            ->with('banner', 'Patient updated successfully!')
            ->with('bannerStyle', 'success');
    }

    public function destroy($id)
    {
        $this->patientService->delete($id);

        return redirect()->route('admin.patients.index')
            ->with('banner', 'Patient deleted successfully!')
            ->with('bannerStyle', 'success');
    }

    // Optimized export methods
    public function export(Request $request)
    {
        return $this->patientService->export($request);
    }

    public function printAll(Request $request)
    {
        return $this->patientService->printAll($request);
    }

    public function printCurrent(Request $request)
    {
        return $this->patientService->printCurrent($request);
    }

    public function printSingle(Request $request, $id)
    {
        return $this->patientService->printSingle($id, $request);
    }

    // API endpoint for quick patient lookup (highly optimized)
    public function quickSearch(Request $request)
    {
        $search = $request->input('q');

        if (strlen($search) < 2) {
            return response()->json([]);
        }

        // Cache quick searches for better performance
        $cacheKey = 'patient_quick_search_'.md5($search);

        $results = \Illuminate\Support\Facades\Cache::remember($cacheKey, 300, function () use ($search) {
            return Patient::where('full_name', 'ilike', "%{$search}%")
                ->orWhere('patient_code', 'ilike', "%{$search}%")
                ->select('id', 'full_name', 'patient_code', 'phone_number')
                ->limit(10)
                ->get();
        });

        return response()->json($results);
    }
}
