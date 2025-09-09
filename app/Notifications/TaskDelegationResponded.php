<?php

namespace App\Notifications;

use App\Models\TaskDelegation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TaskDelegationResponded extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public TaskDelegation $task, public User $actor) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $status = $this->task->acceptance_status ?? 'Pending';
        return [
            'sender_name' => 'Task Response',
            'message_preview' => sprintf('%s %s task "%s"', $this->actor->name ?? 'A staff member', strtolower($status), $this->task->title),
            'url' => route('admin.task-delegations.index'),
            'type' => 'task_delegation_responded',
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'acceptance_status' => $status,
        ];
    }
}

