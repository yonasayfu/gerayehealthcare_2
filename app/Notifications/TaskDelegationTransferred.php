<?php

namespace App\Notifications;

use App\Models\Staff;
use App\Models\TaskDelegation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class TaskDelegationTransferred extends Notification implements ShouldQueue
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
        // Get the new assignee's name
        $newAssignee = Staff::find($this->task->assigned_to);
        $assigneeName = $newAssignee ? $newAssignee->first_name . ' ' . $newAssignee->last_name : 'Unknown Staff';

        // Get the actor's name
        $actorName = $this->actor->name ?? 'A staff member';

        return [
            'sender_name' => 'Task Transfer',
            'message_preview' => sprintf(
                '%s transferred task "%s" to %s',
                $actorName,
                $this->task->title,
                $assigneeName
            ),
            'url' => route('admin.task-delegations.index'),
            'type' => 'task_delegation_transferred',
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'assigned_to' => $this->task->assigned_to,
            'due_date' => $this->task->due_date,
            'status' => $this->task->status,
            'actor_name' => $actorName,
            'assignee_name' => $assigneeName,
        ];
    }

    public function toPush(object $notifiable): array
    {
        $payload = $this->toArray($notifiable);

        return [
            'title' => 'Task transferred',
            'body' => Str::limit($payload['message_preview'] ?? 'A task has been transferred.', 140),
            'data' => $payload,
            'channel' => 'general',
        ];
    }
}
