<?php

namespace App\Http\Controllers\Staff;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\TaskDelegation;
use App\Models\User;
use App\Notifications\TaskDelegationTransferred;
use App\Services\TaskDelegationService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
        $staffId = auth()->user()->staff->id ?? null;
        $requestedScope = $request->input('scope');
        $scope = $requestedScope ?? ($staffId ? 'assigned' : 'created'); // sensible default

        // Sorting parameters
        $sortBy = $request->input('sort_by', 'due_date');
        $sortOrder = $request->input('sort_order', 'asc');
        $search = $request->input('search', '');
        $perPage = $request->input('per_page', 15);

        $query = TaskDelegation::with('assignee');
        if ($scope === 'created') {
            $query->where('created_by', auth()->id());
        } else {
            if ($staffId) {
                $query->where('assigned_to', $staffId);
            } else {
                $query->whereRaw('1=0'); // no staff profile -> no assigned tasks
            }
        }

        // Apply search filter
        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        // Apply sorting
        if (in_array($sortBy, ['title', 'due_date', 'status'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('due_date', 'asc');
        }

        $tasks = $query->paginate($perPage)
            ->withQueryString();

        return Inertia::render('Staff/TaskDelegations/Index', [
            'taskDelegations' => $tasks,
            'filters' => $request->only(['per_page', 'search']) + ['scope' => $scope, 'sort_by' => $sortBy, 'sort_order' => $sortOrder],
            'staff' => Staff::query()->select('id', 'first_name', 'last_name')->orderBy('first_name')->orderBy('last_name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $userStaffId = $user->staff->id ?? null;
        
        // Check if user is super admin (can create tasks without staff profile)
        $isSuperAdmin = $user->hasRole(RoleEnum::SUPER_ADMIN->value);
        
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'due_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'assigned_to' => ['nullable', Rule::exists('staff', 'id')],
            'priority_level' => ['nullable', 'integer', 'min:1', 'max:5'],
            'task_category' => ['nullable', 'string', 'max:100'],
            'estimated_duration_minutes' => ['nullable', 'integer', 'min:1'],
            'is_billable' => ['nullable', 'boolean'],
        ];
        
        // Super admins can create tasks even without a staff profile
        if (!$userStaffId && !$isSuperAdmin) {
            $rules['assigned_to'] = ['required', Rule::exists('staff', 'id')];
        }
        
        $validated = $request->validate($rules);

        // Determine assignee - super admin can assign to anyone or to themselves
        $assignedTo = $validated['assigned_to'] ?? $userStaffId;
        
        // If super admin and no assignee specified, assign to themselves (if they have a staff profile)
        if ($isSuperAdmin && !$assignedTo && $userStaffId) {
            $assignedTo = $userStaffId;
        }

        $this->taskDelegationService->create([
            'title' => $validated['title'],
            'assigned_to' => $assignedTo,
            'due_date' => $validated['due_date'],
            'status' => 'Pending',
            'notes' => $validated['notes'] ?? null,
            'priority_level' => $validated['priority_level'] ?? 3,
            'task_category' => $validated['task_category'] ?? null,
            'estimated_duration_minutes' => $validated['estimated_duration_minutes'] ?? null,
            'is_billable' => $validated['is_billable'] ?? false,
        ]);

        return redirect()->route('staff.task-delegations.index')
            ->with('banner', 'Task created.')->with('bannerStyle', 'success');
    }

    public function update(Request $request, TaskDelegation $task_delegation)
    {
        $validated = $request->validate([
            'status' => ['nullable', Rule::in(['Pending', 'In Progress', 'Completed'])],
            'assigned_to' => ['nullable', Rule::exists('staff', 'id')],
            'notes' => ['nullable', 'string'],
            'acceptance_status' => ['nullable', Rule::in(['Pending', 'Accepted', 'Rejected'])],
            'response_notes' => ['nullable', 'string'],
            'priority_level' => ['nullable', 'integer', 'min:1', 'max:5'],
            'task_category' => ['nullable', 'string', 'max:100'],
            'estimated_duration_minutes' => ['nullable', 'integer', 'min:1'],
            'is_billable' => ['nullable', 'boolean'],
            'progress_status' => ['nullable', Rule::in(['not_started', 'in_progress', 'completed', 'blocked'])],
            'task_date' => ['nullable', 'date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'daily_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $originalAssignee = $task_delegation->assigned_to;

        // Restrict transfer: only assignee or creator can reassign; admins override
        if (array_key_exists('assigned_to', $validated) && (int) $validated['assigned_to'] !== (int) $originalAssignee) {
            $user = auth()->user();
            $isAssignee = (int) ($user->staff->id ?? 0) === (int) $originalAssignee;
            $isCreator = (int) ($task_delegation->created_by ?? 0) === (int) $user->id;
            $isAdmin = method_exists($user, 'hasRole') && ($user->hasRole(RoleEnum::SUPER_ADMIN->value) || $user->hasRole(RoleEnum::ADMIN->value));
            if (!$isAssignee && !$isCreator && !$isAdmin) {
                abort(403, 'You are not allowed to transfer this task.');
            }
        }

        $updated = $this->taskDelegationService->update($task_delegation->id, $validated);

        // If transfer happened, notify admins
        if (array_key_exists('assigned_to', $validated) && (int) $validated['assigned_to'] !== (int) $originalAssignee) {
            $actor = auth()->user();
            $admins = User::role([RoleEnum::SUPER_ADMIN->value, RoleEnum::ADMIN->value])->get();
            foreach ($admins as $admin) {
                $admin->notify(new TaskDelegationTransferred($updated, $actor));
            }
        }

        return back()->with('banner', 'Task updated.')->with('bannerStyle', 'success');
    }
}