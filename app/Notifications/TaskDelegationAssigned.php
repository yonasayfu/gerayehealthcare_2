<?php

namespace App\Notifications;

use App\Models\TaskDelegation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskDelegationAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public TaskDelegation $task) {}

    public function via(object $notifiable): array
    {
        // Deliver to in-app bell and email for parity
        return ['database', 'mail'];
    }

    public function toArray(object $notifiable): array
    {
        // Match frontend NotificationBell.vue expected shape
        return [
            'sender_name' => 'Task Assignment',
            'message_preview' => sprintf(
                'New task: %s (Due: %s, Status: %s)', $this->task->title, $this->task->due_date, $this->task->status
            ),
            // Staff users receive these; send to staff tasks index for a safe landing
            'url' => route('staff.task-delegations.index'),
            // Keep raw fields if needed by other UIs (non-breaking)
            'type' => 'task_delegation_assigned',
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'due_date' => $this->task->due_date,
            'status' => $this->task->status,
        ];
    }

    // Optional: enable email later by adding 'mail' to via()
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Task Assigned')
            ->greeting('Hello '.($notifiable->name ?? ''))
            ->line('You have been assigned a new task: '.$this->task->title)
            ->line('Due: '.($this->task->due_date))
            ->action('View Task', url(route('admin.task-delegations.show', ['task_delegation' => $this->task->id])))
            ->line('Thank you.');
    }
}
