<?php

namespace App\Services;

use App\DTOs\CreateMarketingTaskDTO;
use App\Models\MarketingTask;
use Illuminate\Http\Request;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\AdditionalExportConfigs;

class MarketingTaskService extends BaseService
{
    use ExportableTrait;

    public function __construct(MarketingTask $marketingTask)
    {
        parent::__construct($marketingTask);
    }

    protected function applySearch($query, $search)
    {
        $query->where('title', 'ilike', "%{$search}%")
              ->orWhere('description', 'ilike', "%{$search}%")
              ->orWhere('task_code', 'ilike', "%{$search}%");
    }

    public function getAll(Request $request)
    {
        $query = $this->model->with(['campaign', 'assignedToStaff.user', 'relatedContent', 'doctor.user']);

        if ($request->has('search')) {
            $this->applySearch($query, $request->input('search'));
        }

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

        if ($request->has('sort')) {
            $direction = $request->input('direction', 'asc');
            $query->orderBy($request->input('sort'), $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($request->input('per_page', 10));
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
