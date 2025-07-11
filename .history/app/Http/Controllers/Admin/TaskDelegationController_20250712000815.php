<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaskDelegation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskDelegationController extends Controller
{
    public function index(Request \$request)
    {
        \$search   = \$request->input('search');
        \$sortBy   = \$request->input('sort_by', 'due_date');
        \$sortOrder= \$request->input('sort_order', 'asc');

        $query = TaskDelegation::with('assignee');
        if ($search) {
            $query->where('title', 'like', "%{\$search}%");
        }
        if (in_array(\$sortBy, ['title','due_date','status'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $tasks = $query->paginate(15)->withQueryString();

        return Inertia::render('Admin/TaskDelegations/Index', [
            'taskDelegations' => $tasks,
            'filters' => [
                'search'    => $search,
                'sort_by'   => $sortBy,
                'sort_order'=> $sortOrder,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/TaskDelegations/Create', [
            'staffList' => \App\Models\Staff::select('id','first_name','last_name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'assigned_to' => 'required|exists:staff,id',
            'due_date'    => 'required|date',
            'status'      => 'required|in:Pending,In Progress,Completed',
            'notes'       => 'nullable|string',
        ]);

        TaskDelegation::create($data);
        return redirect()->route('admin.task-delegations.index')
                         ->with('success','Task assigned successfully.');
    }

    public function edit(TaskDelegation \$task_delegation)
    {
        return Inertia::render('Admin/TaskDelegations/Edit', [
            'task'      => $task_delegation,
            'staffList' => \App\Models\Staff::select('id','first_name','last_name')->get(),
        ]);
    }

    public function update(Request $request, TaskDelegation $task_delegation)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'assigned_to' => 'required|exists:staff,id',
            'due_date'    => 'required|date',
            'status'      => 'required|in:Pending,In Progress,Completed',
            'notes'       => 'nullable|string',
        ]);
        $task_delegation->update($data);
        return redirect()->route('admin.task-delegations.index')
                         ->with('success','Task updated successfully.');
    }

    public function destroy(TaskDelegation $task_delegation)
    {
        $task_delegation->delete();
        return back()->with('success','Task deleted.');
    }
}