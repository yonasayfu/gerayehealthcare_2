<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\AdditionalExportConfigs;
use App\Models\TaskDelegation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response as LaravelResponse;
use Inertia\Inertia;

class TaskDelegationController extends Controller
{
    use ExportableTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'due_date');
        $sortOrder = $request->input('sort_order', 'asc');
        $perPage = $request->input('per_page', 15);

        $query = TaskDelegation::with('assignee');

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        if (in_array($sortBy, ['title', 'due_date', 'status'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $tasks = $query->paginate($perPage)
            ->withQueryString();

        return Inertia::render('Admin/TaskDelegations/Index', [
            'taskDelegations' => $tasks,
            'filters' => $request->only(['search', 'sort_by', 'sort_order', 'per_page']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/TaskDelegations/Create', [
            'staffList' => \App\Models\Staff::select('id', 'first_name', 'last_name')->orderBy('first_name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'assigned_to' => 'required|exists:staff,id',
            'due_date' => 'required|date',
            'status' => 'required|in:Pending,In Progress,Completed',
            'notes' => 'nullable|string',
        ]);

        TaskDelegation::create($data);

        return redirect()
            ->route('admin.task-delegations.index', $request->only(['search', 'sort_by', 'sort_order', 'per_page']))
            ->with('success', 'Task assigned successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TaskDelegation $task_delegation)
    {
        return Inertia::render('Admin/TaskDelegations/Edit', [
            'task' => $task_delegation,
            'staffList' => \App\Models\Staff::select('id', 'first_name', 'last_name')->orderBy('first_name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TaskDelegation $task_delegation)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'assigned_to' => 'required|exists:staff,id',
            'due_date' => 'required|date',
            'status' => 'required|in:Pending,In Progress,Completed',
            'notes' => 'nullable|string',
        ]);

        $task_delegation->update($data);

        return redirect()
            ->route('admin.task-delegations.index', $request->only(['search', 'sort_by', 'sort_order', 'per_page']))
            ->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TaskDelegation $task_delegation)
    {
        $task_delegation->delete();

        return back()->with('success', 'Task deleted successfully.');
    }
public function show(TaskDelegation $task_delegation)
{
    return Inertia::render('Admin/TaskDelegations/Show', [
        'task' => $task_delegation->load('assignee'),
    ]);
}
    /**
     * Export the listing to CSV or PDF.
     */
    public function export(Request $request)
    {
        return $this->handleExport($request, TaskDelegation::class, AdditionalExportConfigs::getTaskDelegationConfig());
    }
}
