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

    
}