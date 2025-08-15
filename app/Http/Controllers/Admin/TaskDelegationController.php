<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\TaskDelegationService;
use App\Models\TaskDelegation;
use App\Models\Staff;
use App\Services\Validation\Rules\TaskDelegationRules;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskDelegationController extends BaseController
{
    public function __construct(TaskDelegationService $taskDelegationService)
    {
        parent::__construct(
            $taskDelegationService,
            TaskDelegationRules::class,
            'Admin/TaskDelegations',
            'taskDelegations',
            TaskDelegation::class,
            'App\\DTOs\\CreateTaskDelegationDTO',
            'App\\DTOs\\UpdateTaskDelegationDTO'
        );
    }

    public function create()
    {
        return Inertia::render('Admin/TaskDelegations/Create', [
            'staff' => Staff::all(['id', 'first_name', 'last_name']),
        ]);
    }

    public function edit($id)
    {
        $taskDelegation = $this->service->getById($id);
        return Inertia::render('Admin/TaskDelegations/Edit', [
            'taskDelegation' => $taskDelegation,
            'staff' => Staff::all(['id', 'first_name', 'last_name']),
        ]);
    }
}
