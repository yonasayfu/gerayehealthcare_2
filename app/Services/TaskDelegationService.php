<?php

namespace App\Services;

use App\Models\TaskDelegation;
use Illuminate\Http\Request;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\AdditionalExportConfigs;

class TaskDelegationService extends BaseService
{
    use ExportableTrait;

    public function __construct(TaskDelegation $taskDelegation)
    {
        parent::__construct($taskDelegation);
    }

    protected function applySearch($query, $search)
    {
        return $query->where('title', 'like', "%{$search}%");
    }

    public function getAll(Request $request)
    {
        $query = $this->model->with(['assignee']);

        if ($request->has('search')) {
            $this->applySearch($query, $request->input('search'));
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
        return $this->handleExport($request, TaskDelegation::class, AdditionalExportConfigs::getTaskDelegationConfig());
    }
}
