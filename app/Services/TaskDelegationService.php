<?php

namespace App\Services;

use App\DTOs\CreateTaskDelegationDTO;
use App\Models\TaskDelegation;
use App\Models\Staff;
use App\Notifications\TaskDelegationAssigned;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TaskDelegationService extends BaseService
{
    public function __construct(TaskDelegation $taskDelegation)
    {
        parent::__construct($taskDelegation);
    }

    protected function applySearch($query, $search)
    {
        return $query->where('title', 'like', "%{$search}%");
    }

    public function getAll(Request $request, array $with = [])
    {
        $query = $this->model->with(array_merge(['assignee'], $with));

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

    /**
     * Ensure we eager-load the assignee for show/edit views
     */
    public function getById(int $id, array $with = [])
    {
        $with = array_unique(array_merge(['assignee'], $with));
        $model = $this->model->with($with)->find($id);
        if (!$model) {
            throw new \App\Exceptions\ResourceNotFoundException(class_basename($this->model) . ' not found.');
        }
        return $model;
    }

    /**
     * Create with notification to the assigned staff's user
     */
    public function create(array|object $data)
    {
        $data = is_object($data) ? (array) $data : $data;
        $created = $this->model->create($data);

        try {
            $this->notifyAssignee($created);
        } catch (\Throwable $e) {
            Log::error('Failed to send TaskDelegationAssigned notification on create', [
                'task_id' => $created->id,
                'error' => $e->getMessage(),
            ]);
        }

        return $created;
    }

    /**
     * Update with notification if assigned_to changed
     */
    public function update(int $id, array|object $data)
    {
        $data = is_object($data) ? (array) $data : $data;
        $model = $this->model->findOrFail($id);
        $originalAssignee = $model->assigned_to;

        $model->update($data);

        // If newly created assignment or assignee changed, notify new assignee
        if (array_key_exists('assigned_to', $data) && (int)$data['assigned_to'] !== (int)$originalAssignee) {
            try {
                $this->notifyAssignee($model);
            } catch (\Throwable $e) {
                Log::error('Failed to send TaskDelegationAssigned notification on update', [
                    'task_id' => $model->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $model;
    }

    /**
     * Helper to send notification to the task's assigned staff user
     */
    protected function notifyAssignee(TaskDelegation $task): void
    {
        $staff = Staff::with('user')->find($task->assigned_to);
        if ($staff && $staff->user) {
            $staff->user->notify(new TaskDelegationAssigned($task));
        }
    }
}
