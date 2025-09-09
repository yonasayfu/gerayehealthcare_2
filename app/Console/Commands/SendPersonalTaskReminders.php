<?php

namespace App\Console\Commands;

use App\Models\PersonalTask;
use App\Notifications\PersonalTaskReminder;
use Illuminate\Console\Command;

class SendPersonalTaskReminders extends Command
{
    protected $signature = 'todo:reminders';
    protected $description = 'Send reminders for personal to-do tasks when reminder time is reached';

    public function handle(): int
    {
        $now = now();
        $due = PersonalTask::whereNotNull('reminder_at')
            ->whereNull('reminded_at')
            ->where('reminder_at', '<=', $now)
            ->get();

        foreach ($due as $task) {
            $user = $task->user;
            if ($user) {
                $user->notify(new PersonalTaskReminder($task));
                $task->update(['reminded_at' => $now]);
            }
        }

        $this->info('Processed personal to-do reminders.');
        return self::SUCCESS;
    }
}

