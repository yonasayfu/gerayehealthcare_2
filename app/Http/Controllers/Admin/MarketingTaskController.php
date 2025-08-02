<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\AdditionalExportConfigs;
use App\Http\Requests\StoreMarketingTaskRequest;
use App\Http\Requests\UpdateMarketingTaskRequest;
use App\Models\MarketingTask;
use App\Models\MarketingCampaign;
use App\Models\Staff;
use App\Models\CampaignContent;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Exports\MarketingTasksExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MarketingTaskController extends Controller
{
    use AuthorizesRequests, ExportableTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Inertia\Response
    {
        $this->authorize('viewAny', MarketingTask::class);

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

        $marketingTasks = $query->with(['campaign', 'assignedToStaff.user', 'relatedContent', 'doctor.user'])
                                 ->paginate($request->input('per_page', 5))
                                 ->withQueryString();

                return Inertia::render('Admin/MarketingTasks/Index', [
            'marketingTasks' => $marketingTasks,
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page', 'campaign_id', 'assigned_to_staff_id', 'doctor_id', 'task_type', 'status', 'scheduled_at_start', 'scheduled_at_end', 'related_content_id']),
            'campaigns' => MarketingCampaign::all(),
            'staffs' => Staff::with('user')->get(),
            'campaignContents' => CampaignContent::all(),
            'taskTypes' => ['Email Campaign', 'Social Media Post', 'Ad Creation', 'Content Writing', 'SEO Optimization'],
            'statuses' => ['Pending', 'In Progress', 'Completed', 'Cancelled'],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', MarketingTask::class);

                return Inertia::render('Admin/MarketingTasks/Create', [
            'campaigns' => MarketingCampaign::all(),
            'staffs' => Staff::with('user')->get(),
            'campaignContents' => CampaignContent::all(),
            'taskTypes' => ['Email Campaign', 'Social Media Post', 'Ad Creation', 'Content Writing', 'SEO Optimization'],
            'statuses' => ['Pending', 'In Progress', 'Completed', 'Cancelled'],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMarketingTaskRequest $request)
    {
        $this->authorize('create', MarketingTask::class);

        MarketingTask::create($request->validated());

        return redirect()->route('admin.marketing-tasks.index')->with('success', 'Marketing Task created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MarketingTask $marketingTask)
    {
        $this->authorize('view', $marketingTask);

        $marketingTask->load(['campaign', 'assignedToStaff.user', 'relatedContent', 'doctor.user']);

                return Inertia::render('Admin/MarketingTasks/Show', [
            'marketingTask' => $marketingTask,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MarketingTask $marketingTask)
    {
        $this->authorize('update', $marketingTask);

        $marketingTask->load(['campaign', 'assignedToStaff.user', 'relatedContent', 'doctor.user']);

                return Inertia::render('Admin/MarketingTasks/Edit', [
            'marketingTask' => $marketingTask,
            'campaigns' => MarketingCampaign::all(),
            'staffs' => Staff::with('user')->get(),
            'campaignContents' => CampaignContent::all(),
            'taskTypes' => ['Email Campaign', 'Social Media Post', 'Ad Creation', 'Content Writing', 'SEO Optimization'],
            'statuses' => ['Pending', 'In Progress', 'Completed', 'Cancelled'],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMarketingTaskRequest $request, MarketingTask $marketingTask)
    {
        $this->authorize('update', $marketingTask);

        $marketingTask->update($request->validated());

        return redirect()->route('admin.marketing-tasks.index')->with('success', 'Marketing Task updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MarketingTask $marketingTask)
    {
        $this->authorize('delete', $marketingTask);

        $marketingTask->delete();

        return back()->with('success', 'Marketing Task deleted successfully.');
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, MarketingTask::class, AdditionalExportConfigs::getMarketingTaskConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, MarketingTask::class, AdditionalExportConfigs::getMarketingTaskConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, MarketingTask::class, AdditionalExportConfigs::getMarketingTaskConfig());
    }
}
