<?php

namespace App\Notifications;

use App\Models\PersonalTask;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PersonalTaskReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public PersonalTask $task) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'sender_name' => 'To‑Do Reminder',
            'message_preview' => sprintf('Reminder: "%s"', $this->task->title),
            'url' => route('staff.my-todo.index'),
            'type' => 'personal_task_reminder',
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'due_date' => $this->task->due_date,
        ];
    }
}

