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
            TaskDelegation::class
        );
    }

    public function create()
    {
        return Inertia::render('Admin/TaskDelegations/Create', [
            'staffList' => Staff::select('id', 'first_name', 'last_name')->orderBy('first_name')->get(),
        ]);
    }

    public function show(TaskDelegation $task_delegation)
    {
        return parent::show($task_delegation->id);
    }

    public function edit(TaskDelegation $task_delegation)
    {
        $data = $this->service->getById($task_delegation->id);
        return Inertia::render('Admin/TaskDelegations/Edit', [
            'task' => $data,
            'staffList' => Staff::select('id', 'first_name', 'last_name')->orderBy('first_name')->get(),
        ]);
    }

    public function update(Request $request, TaskDelegation $task_delegation)
    {
        return parent::update($request, $task_delegation->id);
    }

    public function destroy(TaskDelegation $task_delegation)
    {
        return parent::destroy($task_delegation->id);
    }

    public function export(Request $request)
    {
        return parent::export($request);
    }
}