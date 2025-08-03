<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\EventStaffAssignmentService;
use App\Models\EventStaffAssignment;
use App\Services\Validation\Rules\EventStaffAssignmentRules;
use App\Enums\RoleEnum;
use Illuminate\Http\Request;

class EventStaffAssignmentController extends BaseController
{
    public function __construct(EventStaffAssignmentService $eventStaffAssignmentService)
    {
        parent::__construct(
            $eventStaffAssignmentService,
            EventStaffAssignmentRules::class,
            'Admin/EventStaffAssignments',
            'assignments',
            EventStaffAssignment::class
        );
        $this->middleware('role:' . RoleEnum::SUPER_ADMIN->value . '|' . RoleEnum::ADMIN->value);
    }

    public function show(EventStaffAssignment $eventStaffAssignment)
    {
        return parent::show($eventStaffAssignment->id);
    }

    public function edit(EventStaffAssignment $eventStaffAssignment)
    {
        return parent::edit($eventStaffAssignment->id);
    }

    public function update(Request $request, EventStaffAssignment $eventStaffAssignment)
    {
        return parent::update($request, $eventStaffAssignment->id);
    }

    public function destroy(EventStaffAssignment $eventStaffAssignment)
    {
        return parent::destroy($eventStaffAssignment->id);
    }

    public function printSingle(EventStaffAssignment $eventStaffAssignment)
    {
        return parent::printSingle($eventStaffAssignment->id);
    }
}
