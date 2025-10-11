<?php

namespace App\Notifications;

use App\Models\TaskDelegation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class TaskDelegationDueReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public TaskDelegation $task, public string $kind = 'due_today') {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $isStaff = method_exists($notifiable, 'hasRole') && $notifiable->hasRole(\App\Enums\RoleEnum::STAFF->value);
        $message = $this->kind === 'overdue'
            ? sprintf('Overdue: "%s" was due %s', $this->task->title, $this->task->due_date)
            : sprintf('Due Today: "%s" is due %s', $this->task->title, $this->task->due_date);

        return [
            'sender_name' => 'Task Reminder',
            'message_preview' => $message,
            'url' => $isStaff ? route('staff.task-delegations.index') : route('admin.task-delegations.index'),
            'type' => 'task_delegation_due_reminder',
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'due_date' => $this->task->due_date,
            'status' => $this->task->status,
            'kind' => $this->kind,
        ];
    }

    public function toPush(object $notifiable): array
    {
        $payload = $this->toArray($notifiable);

        return [
            'title' => $payload['sender_name'] ?? 'Task reminder',
            'body' => Str::limit($payload['message_preview'] ?? 'You have tasks that need attention.', 140),
            'data' => $payload,
            'channel' => 'general',
        ];
    }
}
