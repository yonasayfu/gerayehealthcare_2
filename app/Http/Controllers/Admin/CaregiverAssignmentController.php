<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreateCaregiverAssignmentDTO;
use App\DTOs\UpdateCaregiverAssignmentDTO;
use App\Http\Controllers\Base\BaseController;
use App\Services\CaregiverAssignmentService;
use App\Models\CaregiverAssignment;
use App\Models\Patient;
use App\Models\Staff;
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
        $patients = Patient::select('id','full_name')->orderBy('full_name')->get();
        $staff = Staff::select('id','first_name','last_name')->orderBy('first_name')->get();
        return Inertia::render($this->viewName . '/Create', compact('patients','staff'));
    }

    public function edit($id)
    {
        $assignment = $this->service->getById($id);
        $patients = Patient::select('id','full_name')->orderBy('full_name')->get();
        $staff = Staff::select('id','first_name','last_name')->orderBy('first_name')->get();
        return Inertia::render($this->viewName . '/Edit', [
            'assignment' => $assignment,
            'patients' => $patients,
            'staff' => $staff,
        ]);
    }

    public function show($id)
    {
        $assignment = $this->service->getById($id);
        $assignment->load(['patient','staff']);
        return Inertia::render($this->viewName . '/Show', [
            'assignment' => $assignment,
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $this->validateRequest($request, 'store');
        $data = $this->dtoClass ? new ($this->dtoClass)(...$this->prepareDataForDTO($validatedData)) : $validatedData;
        $this->service->create($data);
        return redirect()->route('admin.assignments.index')->with('success', 'Assignment created successfully.');
    }

    public function update(Request $request, $id)
    {
        $model = $this->service->getById($id);
        $validatedData = $this->validateRequest($request, 'update', $model);
        $data = $this->dtoClass ? new (UpdateCaregiverAssignmentDTO::class)(...$this->prepareDataForDTO($validatedData)) : $validatedData;
        $this->service->update($id, $data);
        return redirect()->route('admin.assignments.index')->with('success', 'Assignment updated successfully.');
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return redirect()->route('admin.assignments.index')->with('success', 'Assignment deleted successfully.');
    }
}