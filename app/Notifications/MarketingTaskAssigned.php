<?php

namespace App\Notifications;

use App\Models\MarketingTask;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class MarketingTaskAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public MarketingTask $task) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New Marketing Task Assigned',
            'message' => $this->task->title ?: $this->task->task_code,
            'type' => 'marketing_task_assigned',
            'task_id' => $this->task->id,
            'url' => route('admin.marketing-tasks.show', $this->task->id),
        ];
    }
}

