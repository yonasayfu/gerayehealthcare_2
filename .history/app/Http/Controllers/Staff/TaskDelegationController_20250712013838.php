<?php
namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\TaskDelegation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskDelegationController extends Controller
{
    public function index(Request $request)
    {
        $staffId = auth()->user()->staff->id;

        $tasks = TaskDelegation::with('assignee')
            ->where('assigned_to', $staffId)
            ->orderBy('due_date','asc')
            ->paginate($request->input('per_page', 15))
            ->withQueryString();

        return Inertia::render('Staff/TaskDelegations/Index', [
            'taskDelegations' => $tasks,
            'filters'         => $request->only(['per_page']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'    => 'required|string|max:255',
            'due_date' => 'required|date',
            'notes'    => 'nullable|string',
        ]);

        // assign to self
        $data['assigned_to'] = auth()->user()->staff->id;
        $data['status']      = 'Pending';

        TaskDelegation::create($data);

        return redirect()->route('staff.task-delegations.index')
                         ->with('success','Task created.');
    }

    public function update(Request $request, TaskDelegation $task_delegation)
    {
        $data = $request->validate([
            'status' => 'required|in:Pending,In Progress,Completed',
        ]);

        $task_delegation->update($data);

        return back()->with('success','Task status updated.');
    }
    public function update(Request $request, TaskDelegation $task_delegation)
    {
        $data = $request->validate([
            'status' => 'required|in:Pending,In Progress,Completed',
        ]);

        $task_delegation->update($data);

        // Use Inertia::location to force a fresh re-fetch of the index props
        return Inertia::location(route('staff.task-delegations.index'));
    }
}
