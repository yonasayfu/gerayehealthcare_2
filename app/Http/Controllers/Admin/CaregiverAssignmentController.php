<?php

namespace App\Http\Controllers\Admin;

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
            CaregiverAssignment::class
        );
    }

    public function create()
    {
        return Inertia::render('Admin/CaregiverAssignments/Create', [
            'staff' => Staff::where('status', 'Active')->orderBy('first_name')->get(),
            'patients' => Patient::orderBy('full_name')->get()
        ]);
    }

    public function show(CaregiverAssignment $assignment)
    {
        return parent::show($assignment->id);
    }

    public function edit(CaregiverAssignment $assignment)
    {
        $data = $this->service->getById($assignment->id);
        return Inertia::render('Admin/CaregiverAssignments/Edit', [
            'assignment' => $data,
            'staff' => Staff::where('status', 'Active')->orderBy('first_name')->get(),
            'patients' => Patient::orderBy('full_name')->get()
        ]);
    }

    public function update(Request $request, CaregiverAssignment $assignment)
    {
        return parent::update($request, $assignment->id);
    }

    public function destroy(CaregiverAssignment $assignment)
    {
        return parent::destroy($assignment->id);
    }

    public function printSingle(CaregiverAssignment $assignment)
    {
        return parent::printSingle($assignment->id);
    }
}