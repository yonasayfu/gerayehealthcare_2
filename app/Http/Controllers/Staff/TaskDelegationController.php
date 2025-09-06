<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStaffTaskDelegationRequest;
use App\Models\TaskDelegation;
use App\Services\TaskDelegationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskDelegationController extends Controller
{
    protected $taskDelegationService;

    public function __construct(TaskDelegationService $taskDelegationService)
    {
        $this->taskDelegationService = $taskDelegationService;
    }

    public function index(Request $request)
    {
        $staffId = auth()->user()->staff->id;

        $tasks = TaskDelegation::with('assignee')
            ->where('assigned_to', $staffId)
            ->orderBy('due_date', 'asc')
            ->paginate($request->input('per_page', 15))
            ->withQueryString();

        return Inertia::render('Staff/TaskDelegations/Index', [
            'taskDelegations' => $tasks,
            'filters' => $request->only(['per_page']),
        ]);
    }

    public function store(StoreStaffTaskDelegationRequest $request)
    {
        $validated = $request->validated();
        $dto = new CreateTaskDelegationDTO(
            title: $validated['title'],
            assigned_to: auth()->user()->staff->id,
            due_date: $validated['due_date'],
            status: 'Pending',
            notes: $validated['notes'] ?? null
        );

        $this->taskDelegationService->create($dto);

        return redirect()->route('staff.task-delegations.index')
            ->with('banner', 'Task created.')->with('bannerStyle', 'success');
    }
}
