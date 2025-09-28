<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Hourly auto-checkout of long-running visits
        $schedule->command('visits:auto-checkout')->hourly();
        // Daily task reminders
        $schedule->command('tasks:due-reminders')->dailyAt('08:00');
        // Personal to-do reminders (runs every 5 minutes)
        $schedule->command('todo:reminders')->everyFiveMinutes();
        // Spawn recurring personal tasks into My Day shortly after midnight
        $schedule->command('todo:spawn-recurring')->dailyAt('00:05');
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
