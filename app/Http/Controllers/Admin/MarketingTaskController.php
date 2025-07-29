<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMarketingTaskRequest;
use App\Http\Requests\UpdateMarketingTaskRequest;
use App\Models\MarketingTask;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Exports\MarketingTasksExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class MarketingTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Inertia\Response
    {
        $query = MarketingTask::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('title', 'ilike', "%{$search}%")
                  ->orWhere('description', 'ilike', "%{$search}%")
                  ->orWhere('task_code', 'ilike', "%{$search}%");
        }

        // Filtering
        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', $request->input('campaign_id'));
        }
        if ($request->filled('assigned_to_staff_id')) {
            $query->where('assigned_to_staff_id', $request->input('assigned_to_staff_id'));
        }
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->input('doctor_id'));
        }
        if ($request->filled('task_type')) {
            $query->where('task_type', $request->input('task_type'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('scheduled_at_start')) {
            $query->where('scheduled_at', '>=', $request->input('scheduled_at_start'));
        }
        if ($request->filled('scheduled_at_end')) {
            $query->where('scheduled_at', '<=', $request->input('scheduled_at_end'));
        }

        // Sorting
        if ($request->filled('sort') && !empty($request->input('sort'))) {
            $query->orderBy($request->input('sort'), $request->input('direction', 'asc'));
        } else {
            $query->orderBy('scheduled_at', 'asc');
        }

        $marketingTasks = $query->with(['campaign', 'assignedToStaff', 'relatedContent', 'doctor'])
                                 ->paginate($request->input('per_page', 10))
                                 ->withQueryString();

        return Inertia::render('Admin/MarketingTasks/Index', [
            'marketingTasks' => $marketingTasks,
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page', 'campaign_id', 'assigned_to_staff_id', 'doctor_id', 'task_type', 'status', 'scheduled_at_start', 'scheduled_at_end']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/MarketingTasks/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMarketingTaskRequest $request)
    {
        MarketingTask::create($request->validated());

        return redirect()->route('admin.marketing-tasks.index')->with('success', 'Marketing Task created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MarketingTask $marketingTask)
    {
        $marketingTask->load(['campaign', 'assignedToStaff', 'relatedContent', 'doctor']);

        return Inertia::render('Admin/MarketingTasks/Show', [
            'marketingTask' => $marketingTask,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MarketingTask $marketingTask)
    {
        $marketingTask->load(['campaign', 'assignedToStaff', 'relatedContent', 'doctor']);

        return Inertia::render('Admin/MarketingTasks/Edit', [
            'marketingTask' => $marketingTask,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMarketingTaskRequest $request, MarketingTask $marketingTask)
    {
        $marketingTask->update($request->validated());

        return redirect()->route('admin.marketing-tasks.index')->with('success', 'Marketing Task updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MarketingTask $marketingTask)
    {
        $marketingTask->delete();

        return back()->with('success', 'Marketing Task deleted successfully.');
    }

    public function export(Request $request)
    {
        $type = $request->input('type');
        if ($type === 'csv') {
            return Excel::download(new MarketingTasksExport, 'marketing-tasks.csv');
        } elseif ($type === 'pdf') {
            $marketingTasks = MarketingTask::with(['campaign', 'assignedToStaff', 'relatedContent', 'doctor'])->get();
            $pdf = Pdf::loadView('pdf.marketing-tasks', compact('marketingTasks'));
            return $pdf->download('marketing-tasks.pdf');
        }
        return redirect()->back()->with('error', 'Invalid export type.');
    }

    public function printAll(Request $request)
    {
        $marketingTasks = MarketingTask::with(['campaign', 'assignedToStaff', 'relatedContent', 'doctor'])->get();
        return Inertia::render('Admin/MarketingTasks/PrintAll', [
            'marketingTasks' => $marketingTasks,
        ]);
    }

    public function printCurrent(Request $request)
    {
        $query = MarketingTask::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('title', 'ilike', "%{$search}%")
                  ->orWhere('description', 'ilike', "%{$search}%")
                  ->orWhere('task_code', 'ilike', "%{$search}%");
        }

        // Filtering
        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', $request->input('campaign_id'));
        }
        if ($request->filled('assigned_to_staff_id')) {
            $query->where('assigned_to_staff_id', $request->input('assigned_to_staff_id'));
        }
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->input('doctor_id'));
        }
        if ($request->filled('task_type')) {
            $query->where('task_type', $request->input('task_type'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('scheduled_at_start')) {
            $query->where('scheduled_at', '>=', $request->input('scheduled_at_start'));
        }
        if ($request->filled('scheduled_at_end')) {
            $query->where('scheduled_at', '<=', $request->input('scheduled_at_end'));
        }

        $marketingTasks = $query->with(['campaign', 'assignedToStaff', 'relatedContent', 'doctor'])->get();

        return Inertia::render('Admin/MarketingTasks/PrintCurrent', [
            'marketingTasks' => $marketingTasks,
        ]);
    }
}
