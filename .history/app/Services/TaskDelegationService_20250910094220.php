<?php

namespace App\Services;

use App\Models\Staff;
use App\Models\TaskDelegation;
use App\Notifications\TaskDelegationAssigned;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $query = $this->model->with(array_merge(['assignee', 'creatorUser'], $with));

        if ($request->has('search')) {
            $this->applySearch($query, $request->input('search'));
        }

        // Handle sorting
        if ($request->has('sort_by')) {
            $sortBy = $request->input('sort_by');
            $sortOrder = $request->input('sort_order', 'asc');

            // Validate sort fields to prevent SQL injection
            $allowedSortFields = ['title', 'due_date', 'status', 'created_at'];
            if (in_array($sortBy, $allowedSortFields)) {
                $query->orderBy($sortBy, $sortOrder);
            } else {
                $query->orderBy('created_at', 'desc');
            }
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
        $with = array_unique(array_merge(['assignee', 'creatorUser'], $with));
        $model = $this->model->with($with)->find($id);
        if (!$model) {
            throw new \App\Exceptions\ResourceNotFoundException(class_basename($this->model) . ' not found.');
        }

        return $model;
    }

    /**
     * Create with notification to the assigned staff's user
     */
    public function create(array | object $data)
    {
        $data = is_object($data) ? (array) $data : $data;
        // Stamp creator user if available
        if (!array_key_exists('created_by', $data)) {
            $data['created_by'] = Auth::id();
        }
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
    public function update(int $id, array | object $data)
    {
        $data = is_object($data) ? (array) $data : $data;
        $model = $this->model->findOrFail($id);
        $originalAssignee = $model->assigned_to;
        $originalStatus = $model->status;
        $originalAcceptance = $model->acceptance_status ?? null;

        // If acceptance_status provided, stamp responder fields
        if (array_key_exists('acceptance_status', $data)) {
            $data['responded_by'] = auth()->id();
            $data['responded_at'] = now();
        }

        $model->update($data);

        // If newly created assignment or assignee changed, notify new assignee
        if (array_key_exists('assigned_to', $data) && (int) $data['assigned_to'] !== (int) $originalAssignee) {
            try {
                $this->notifyAssignee($model);
            } catch (\Throwable $e) {
                Log::error('Failed to send TaskDelegationAssigned notification on update', [
                    'task_id' => $model->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // If status transitioned to Completed, notify assignee and super admins
        if (array_key_exists('status', $data) && $originalStatus !== 'Completed' && $model->status === 'Completed') {
            try {
                $this->notifyCompletion($model);
            } catch (\Throwable $e) {
                Log::error('Failed to send TaskDelegationCompleted notification on update', [
                    'task_id' => $model->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // If acceptance status changed, notify creator and admins
        if (array_key_exists('acceptance_status', $data) && $data['acceptance_status'] !== $originalAcceptance) {
            try {
                $this->notifyResponse($model);
            } catch (\Throwable $e) {
                Log::error('Failed to send TaskDelegationResponded notification', [
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

    protected function notifyCompletion(TaskDelegation $task): void
    {
        // Notify the assignee's user
        $assignee = Staff::with('user')->find($task->assigned_to);
        if ($assignee && $assignee->user) {
            $assignee->user->notify(new \App\Notifications\TaskDelegationCompleted($task));
        }

        // Notify super admins
        $superAdmins = \App\Models\User::role([\App\Enums\RoleEnum::SUPER_ADMIN->value])->get();
        foreach ($superAdmins as $admin) {
            $admin->notify(new \App\Notifications\TaskDelegationCompleted($task));
        }
    }

    protected function notifyResponse(TaskDelegation $task): void
    {
        $actor = auth()->user();
        // Notify task creator if exists
        if ($task->created_by) {
            $creator = \App\Models\User::find($task->created_by);
            if ($creator) {
                $creator->notify(new \App\Notifications\TaskDelegationResponded($task, $actor));
            }
        }
        // Notify admins
        $admins = \App\Models\User::role([\App\Enums\RoleEnum::SUPER_ADMIN->value, \App\Enums\RoleEnum::ADMIN->value])->get();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\TaskDelegationResponded($task, $actor));
        }
    }
}
