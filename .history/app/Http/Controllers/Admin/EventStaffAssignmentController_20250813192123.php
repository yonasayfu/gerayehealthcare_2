<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Models\Event;
use App\Models\Staff;
use App\Models\EventStaffAssignment;
use App\Services\EventStaffAssignmentService;
use App\Services\Validation\Rules\EventStaffAssignmentRules;
use App\DTOs\CreateEventStaffAssignmentDTO;
use App\Enums\RoleEnum;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EventStaffAssignmentController extends BaseController
{
    public function __construct(EventStaffAssignmentService $eventStaffAssignmentService)
    {
        parent::__construct(
            $eventStaffAssignmentService,
            EventStaffAssignmentRules::class,
            'Admin/EventStaffAssignments',
            'assignments',
            EventStaffAssignment::class,
            CreateEventStaffAssignmentDTO::class
        );
        $this->middleware('role:' . RoleEnum::SUPER_ADMIN->value . '|' . RoleEnum::ADMIN->value);
    }

    public function create()
    {
        $events = Event::select('id', 'title')->orderBy('title')->get();
        $staff = Staff::select('id', 'first_name', 'last_name')->orderBy('first_name')->get();

        return Inertia::render($this->viewName . '/Create', [
            'events' => $events,
            'staff' => $staff,
        ]);
    }

    public function edit($id)
    {
        $eventStaffAssignment = $this->service->getById($id);
        $events = Event::select('id', 'title')->orderBy('title')->get();
        $staff = Staff::select('id', 'first_name', 'last_name')->orderBy('first_name')->get();

        return Inertia::render($this->viewName . '/Edit', [
            lcfirst(class_basename($this->modelClass)) => $eventStaffAssignment,
            'events' => $events,
            'staff' => $staff,
        ]);
    }
}
