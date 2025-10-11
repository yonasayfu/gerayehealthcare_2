<?php

namespace App\Notifications;

use App\Models\Staff;
use App\Models\TaskDelegation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class TaskDelegationResponded extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public TaskDelegation $task, public User $actor)
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

        $status = $this->task->acceptance_status ?? 'Pending';
        return [
            'sender_name' => 'Task Response',
            'message_preview' => sprintf('%s %s task "%s" assigned to %s', $this->actor->name ?? 'A staff member', strtolower($status), $this->task->title, $assigneeName),
            'url' => route('admin.task-delegations.index'),
            'type' => 'task_delegation_responded',
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'acceptance_status' => $status,
            'actor_name' => $this->actor->name ?? 'A staff member',
            'assignee_name' => $assigneeName,
        ];
    }

    public function toPush(object $notifiable): array
    {
        $payload = $this->toArray($notifiable);

        return [
            'title' => 'Task response',
            'body' => Str::limit($payload['message_preview'] ?? 'A task response was recorded.', 140),
            'data' => $payload,
            'channel' => 'general',
        ];
    }
}
