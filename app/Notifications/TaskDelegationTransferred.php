<?php

namespace App\Notifications;

use App\Models\TaskDelegation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TaskDelegationTransferred extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public TaskDelegation $task, public User $actor) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'sender_name' => 'Task Transfer',
            'message_preview' => sprintf(
                '%s transferred task "%s" to staff ID %d',
                $this->actor->name ?? 'A staff member',
                $this->task->title,
                $this->task->assigned_to
            ),
            'url' => route('admin.task-delegations.index'),
            'type' => 'task_delegation_transferred',
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'assigned_to' => $this->task->assigned_to,
            'due_date' => $this->task->due_date,
            'status' => $this->task->status,
        ];
    }
}

