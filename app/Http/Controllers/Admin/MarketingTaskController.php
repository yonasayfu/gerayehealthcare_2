<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreateMarketingTaskDTO;
use App\Http\Controllers\Base\BaseController;
use App\Models\MarketingTask;
use App\Services\CachedDropdownService;
use App\Services\MarketingTaskService;
use App\Services\Validation\Rules\MarketingTaskRules;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MarketingTaskController extends BaseController
{
    use AuthorizesRequests;

    public function __construct(MarketingTaskService $marketingTaskService)
    {
        parent::__construct(
            $marketingTaskService,
            MarketingTaskRules::class,
            'Admin/MarketingTasks',
            'marketingTasks',
            MarketingTask::class,
            CreateMarketingTaskDTO::class
        );
    }

    /**
     * Provide list data needed by filters on index (campaigns, staffs, task types).
     */
    public function index(Request $request)
    {
        $data = $this->service->getAll($request);

        // OPTIMIZED: Use cached dropdown service
        $campaigns = CachedDropdownService::getMarketingCampaigns();
        $staffs = CachedDropdownService::getStaffWithUsers();

        // Task types and statuses: align with factory and UI options
        $taskTypes = [
            'Email Campaign',
            'Social Media Post',
            'Ad Creation',
            'Content Writing',
            'SEO Optimization',
        ];
        $filters = $request->only([
            'search',
            'sort',
            'direction',
            'per_page',
            'campaign_id',
            'assigned_to_staff_id',
            'task_type',
        ]);

        return Inertia::render($this->viewName.'/Index', [
            $this->dataVariableName => $data,
            'campaigns' => $campaigns,
            'staffs' => $staffs,
            'taskTypes' => $taskTypes,
            'filters' => $filters,
        ]);
    }

    /**
     * Provide dropdown datasets for create form, similar to Patient module pattern.
     */
    public function create()
    {
        // OPTIMIZED: Use cached dropdown service
        $campaigns = CachedDropdownService::getMarketingCampaigns();
        $staffMembers = CachedDropdownService::getStaffWithUsers();
        $contents = CachedDropdownService::getCampaignContent();

        $taskTypes = [
            'Email Campaign',
            'Social Media Post',
            'Ad Creation',
            'Content Writing',
            'SEO Optimization',
        ];
        $statuses = ['Pending', 'In Progress', 'Completed', 'Cancelled'];

        return Inertia::render($this->viewName.'/Create', [
            'campaigns' => $campaigns,
            'staffMembers' => $staffMembers,
            'contents' => $contents,
            'taskTypes' => $taskTypes,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Provide dropdown datasets and record for edit form.
     */
    public function edit($id)
    {
        $data = $this->service->getById($id);

        // OPTIMIZED: Use cached dropdown service
        $campaigns = CachedDropdownService::getMarketingCampaigns();
        $staffs = CachedDropdownService::getStaffWithUsers();
        $campaignContents = CachedDropdownService::getCampaignContent();

        $taskTypes = [
            'Email Campaign',
            'Social Media Post',
            'Ad Creation',
            'Content Writing',
            'SEO Optimization',
        ];
        $statuses = ['Pending', 'In Progress', 'Completed', 'Cancelled'];

        return Inertia::render($this->viewName.'/Edit', [
            'marketingTask' => $data,
            'campaigns' => $campaigns,
            'staffs' => $staffs,
            'campaignContents' => $campaignContents,
            'taskTypes' => $taskTypes,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Print current view via centralized export system.
     */
    public function printCurrent(Request $request)
    {
        return $this->service->printCurrent($request);
    }

    /**
     * Print all records via centralized export system.
     */
    public function printAll(Request $request)
    {
        return $this->service->printAll($request);
    }
}
