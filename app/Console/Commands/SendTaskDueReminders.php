<?php

namespace App\Console\Commands;

use App\Models\TaskDelegation;
use App\Models\User;
use App\Notifications\TaskDelegationDueReminder;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendTaskDueReminders extends Command
{
    protected $signature = 'tasks:due-reminders';
    protected $description = 'Send reminders for tasks due today and overdue';

    public function handle(): int
    {
        $today = Carbon::today();

        // Due today (not completed)
        $dueToday = TaskDelegation::whereDate('due_date', $today)
            ->where('status', '!=', 'Completed')
            ->get();

        foreach ($dueToday as $task) {
            $this->notifyAssignee($task, 'due_today');
        }

        // Overdue (past due date, not completed)
        $overdue = TaskDelegation::whereDate('due_date', '<', $today)
            ->where('status', '!=', 'Completed')
            ->get();

        foreach ($overdue as $task) {
            $this->notifyAssignee($task, 'overdue');
        }

        $this->info('Task due reminders processed.');
        return self::SUCCESS;
    }

    protected function notifyAssignee(TaskDelegation $task, string $kind): void
    {
        $assignee = $task->assignee?->user;
        if ($assignee) {
            $assignee->notify(new TaskDelegationDueReminder($task, $kind));
        }
    }
}

