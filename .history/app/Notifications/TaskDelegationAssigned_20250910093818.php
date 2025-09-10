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
        // Get creator information if available
        $creator = null;
        if ($this->task->created_by) {
            $creatorUser = \App\Models\User::find($this->task->created_by);
            $creator = $creatorUser ? $creatorUser->name : null;
        }
        
        // Match frontend NotificationBell.vue expected shape
        return [
            'sender_name' => 'Task Assignment',
            'message_preview' => sprintf(
                'New task: %s (Due: %s, Status: %s)%s',
                $this->task->title,
                $this->task->due_date,
                $this->task->status,
                $creator ? " from {$creator}" : ""
            ),
            // Staff users receive these; send to staff tasks index for a safe landing
            'url' => route('staff.task-delegations.index'),
            // Keep raw fields if needed by other UIs (non-breaking)
            'type' => 'task_delegation_assigned',
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'due_date' => $this->task->due_date,
            'status' => $this->task->status,
            'creator' => $creator,
        ];
    }

    // Optional: enable email later by adding 'mail' to via()
    public function toMail(object $notifiable): MailMessage
    {
        $creator = null;
        if ($this->task->created_by) {
            $creatorUser = \App\Models\User::find($this->task->created_by);
            $creator = $creatorUser ? $creatorUser->name : null;
        }
        
        $mail = (new MailMessage)
            ->subject('New Task Assigned')
            ->greeting('Hello '.($notifiable->name ?? ''))
            ->line('You have been assigned a new task: '.$this->task->title)
            ->line('Due: '.($this->task->due_date))
            ->line('Status: '.$this->task->status);
            
        if ($creator) {
            $mail->line("Assigned by: {$creator}");
        }
            
        return $mail->action('View Task', url(route('staff.task-delegations.index')))
            ->line('Thank you.');
    }
}