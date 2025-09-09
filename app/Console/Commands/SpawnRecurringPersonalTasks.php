<?php

namespace App\Console\Commands;

use App\Models\PersonalTask;
use Illuminate\Console\Command;

class SpawnRecurringPersonalTasks extends Command
{
    protected $signature = 'todo:spawn-recurring';
    protected $description = 'Spawn My Day entries for recurring personal tasks';

    public function handle(): int
    {
        $today = now()->toDateString();
        $dow = now()->dayOfWeek; // 0 (Sun) .. 6 (Sat)

        // Daily recurrence: ensure a My Day entry exists today
        $daily = PersonalTask::where('recurrence_type', 'daily')->get();
        foreach ($daily as $task) {
            $this->ensureMyDay($task, $today);
        }

        // Weekly recurrence where today matches configured weekdays
        $weekly = PersonalTask::where('recurrence_type', 'weekly')->get();
        foreach ($weekly as $task) {
            $days = (array) ($task->recurrence_weekdays ?? []);
            if (in_array($dow, $days)) {
                $this->ensureMyDay($task, $today);
            }
        }

        $this->info('Spawned recurring personal tasks for My Day where needed.');
        return self::SUCCESS;
    }

    protected function ensureMyDay(PersonalTask $task, string $today): void
    {
        // If there is already an uncompleted My Day task today for this user with same title, skip
        $exists = PersonalTask::where('user_id', $task->user_id)
            ->where('title', $task->title)
            ->where('recurrence_type', $task->recurrence_type)
            ->whereDate('my_day_for', $today)
            ->where('is_completed', false)
            ->exists();
        if ($exists) return;

        // If the source task itself is not completed, we can set its my_day_for to today
        if (!$task->is_completed) {
            $task->update(['my_day_for' => $today]);
            return;
        }

        // Otherwise, create a fresh occurrence (duplicate core fields)
        PersonalTask::create([
            'user_id' => $task->user_id,
            'title' => $task->title,
            'notes' => $task->notes,
            'due_date' => now(),
            'is_completed' => false,
            'is_important' => $task->is_important,
            'reminder_at' => null,
            'my_day_for' => $today,
            'recurrence_type' => $task->recurrence_type,
            'recurrence_weekdays' => $task->recurrence_weekdays,
        ]);
    }
}
