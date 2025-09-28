<?php

namespace App\Notifications;

use App\Models\Staff;
use App\Models\TaskDelegation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TaskDelegationCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public TaskDelegation $task)
    {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        // Get assignee information
        $assignee = Staff::find($this->task->assigned_to);
        $assigneeName = $assignee ? $assignee->first_name . ' ' . $assignee->last_name : 'Unknown Staff';

        // Route based on recipient role: staff go to My Tasks; others to admin list
        $isStaff = method_exists($notifiable, 'hasRole') && $notifiable->hasRole(\App\Enums\RoleEnum::STAFF->value);
        return [
            'sender_name' => 'Task Completed',
            'message_preview' => sprintf('Task "%s" completed by %s', $this->task->title, $assigneeName),
            'url' => $isStaff ? route('staff.task-delegations.index') : route('admin.task-delegations.index'),
            'type' => 'task_delegation_completed',
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'due_date' => $this->task->due_date,
            'status' => $this->task->status,
            'assignee_name' => $assigneeName,
        ];
    }
}
