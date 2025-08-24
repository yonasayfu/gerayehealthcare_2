<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreateCaregiverAssignmentDTO;
use App\DTOs\UpdateCaregiverAssignmentDTO;
use App\Http\Controllers\Base\BaseController;
use App\Models\CaregiverAssignment;
use App\Models\Patient;
use App\Models\Staff;
use App\Services\CaregiverAssignmentService;
use App\Services\CachedDropdownService;
use App\Services\Validation\Rules\CaregiverAssignmentRules;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CaregiverAssignmentController extends BaseController
{
    public function __construct(CaregiverAssignmentService $caregiverAssignmentService)
    {
        parent::__construct(
            $caregiverAssignmentService,
            CaregiverAssignmentRules::class,
            'Admin/CaregiverAssignments',
            'assignments',
            CaregiverAssignment::class,
            CreateCaregiverAssignmentDTO::class
        );
    }

    public function create()
    {
        // OPTIMIZED: Use cached dropdown service
        $patients = CachedDropdownService::getPatients();
        $staff = CachedDropdownService::getActiveStaff();

        return Inertia::render($this->viewName . '/Create', compact('patients', 'staff'));
    }

    public function edit($id)
    {
        $assignment = $this->service->getById($id);
        // OPTIMIZED: Use cached dropdown service
        $patients = CachedDropdownService::getPatients();
        $staff = CachedDropdownService::getActiveStaff();

        return Inertia::render($this->viewName . '/Edit', [
            'assignment' => $assignment,
            'patients' => $patients,
            'staff' => $staff,
        ]);
    }

    public function show($id)
    {
        $assignment = $this->service->getById($id);
        $assignment->load(['patient', 'staff']);

        return Inertia::render($this->viewName . '/Show', [
            'assignment' => $assignment,
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $this->validateRequest($request, 'store');
        $data = $this->dtoClass ? new ($this->dtoClass)(...$this->prepareDataForDTO($validatedData)) : $validatedData;
        $this->service->create($data);

        return redirect()->route('admin.assignments.index')->with('banner', 'Assignment created successfully.')->with('bannerStyle', 'success');
    }

    public function update(Request $request, $id)
    {
        $model = $this->service->getById($id);
        $validatedData = $this->validateRequest($request, 'update', $model);
        $data = $this->dtoClass ? new (UpdateCaregiverAssignmentDTO::class)(...$this->prepareDataForDTO($validatedData)) : $validatedData;
        $this->service->update($id, $data);

        return redirect()->route('admin.assignments.index')->with('banner', 'Assignment updated successfully.')->with('bannerStyle', 'success');
    }

    public function destroy(Request $request, $id)
    {
        $this->service->delete($id);

        return redirect()->route('admin.assignments.index')->with('banner', 'Assignment deleted successfully.')->with('bannerStyle', 'danger');
    }
}
