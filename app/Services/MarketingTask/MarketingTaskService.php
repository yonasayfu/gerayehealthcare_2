<?php

namespace App\Services\MarketingTask;

use App\Http\Config\AdditionalExportConfigs;
use App\Http\Traits\ExportableTrait;
use App\Models\MarketingTask;
use App\Models\Staff;
use App\Models\User;
use App\Enums\RoleEnum;
use App\Notifications\MarketingTaskAssigned;
use App\Services\Base\BaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MarketingTaskService extends BaseService
{
    use ExportableTrait;

    public function __construct(MarketingTask $marketingTask)
    {
        parent::__construct($marketingTask);
    }

    public function getById(int $id, array $with = [])
    {
        // Ensure relationships are loaded for Show/Edit pages
        $defaultWith = ['campaign', 'assignedToStaff.user', 'relatedContent', 'doctor.user'];
        $with = array_unique(array_merge($defaultWith, $with));
        $query = $this->model->newQuery()->with($with);
        $model = $query->findOrFail($id);

        return $model;
    }

    protected function applySearch($query, $search)
    {
        $query->where('title', 'ilike', "%{$search}%")
            ->orWhere('description', 'ilike', "%{$search}%")
            ->orWhere('task_code', 'ilike', "%{$search}%");
    }

    public function getAll(Request $request, array $with = [])
    {
        $query = $this->model->with(array_merge(['campaign', 'assignedToStaff.user', 'relatedContent', 'doctor.user'], $with));

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

        return $query->paginate($request->input('per_page', 5))->withQueryString();
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

    /**
     * Create task and notify assignee + key roles.
     */
    public function create(array|object $data)
    {
        $payload = is_object($data) ? (array) $data : $data;
        $created = parent::create($payload);

        $this->notifyAssignment($created);

        return $created;
    }

    /**
     * Update task; if assignee changed, notify new assignee and key roles.
     */
    public function update(int $id, array|object $data)
    {
        $payload = is_object($data) ? (array) $data : $data;
        $model = $this->model->findOrFail($id);
        $originalAssignee = $model->assigned_to_staff_id;

        $updated = parent::update($id, $payload);

        if (array_key_exists('assigned_to_staff_id', $payload) && (int) $payload['assigned_to_staff_id'] !== (int) $originalAssignee) {
            $this->notifyAssignment($updated);
        }

        return $updated;
    }

    protected function notifyAssignment(MarketingTask $task): void
    {
        try {
            // Notify assigned staff user
            if ($task->assigned_to_staff_id) {
                $staff = Staff::with('user')->find($task->assigned_to_staff_id);
                if ($staff && $staff->user) {
                    $staff->user->notify(new MarketingTaskAssigned($task));
                }
            }

            // Notify leadership roles
            $roles = [RoleEnum::SUPER_ADMIN->value, RoleEnum::ADMIN->value, RoleEnum::CEO->value, RoleEnum::COO->value];
            $recipients = User::role($roles)->get();
            foreach ($recipients as $user) {
                $user->notify(new MarketingTaskAssigned($task));
            }
        } catch (\Throwable $e) {
            Log::error('Failed to send MarketingTaskAssigned notification', [
                'task_id' => $task->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
