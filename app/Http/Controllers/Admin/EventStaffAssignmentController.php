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
            EventStaffAssignment::class,
            CreateEventStaffAssignmentDTO::class
        );
        $this->middleware('role:' . RoleEnum::SUPER_ADMIN->value . '|' . RoleEnum::ADMIN->value);
    }

   
}
