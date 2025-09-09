<?php

namespace App\Console\Commands;

use App\Models\VisitService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class AutoCheckoutVisits extends Command
{
    protected $signature = 'visits:auto-checkout {--hours=8 : Max hours before auto checkout}';

    protected $description = 'Automatically check out visits left in progress beyond a maximum duration';

    public function handle(): int
    {
        $maxHours = (int) $this->option('hours');
        $now = now();
        $cutoff = $now->copy()->subHours($maxHours);

        $query = VisitService::query()
            ->where('status', 'In Progress')
            ->whereNotNull('check_in_time')
            ->where(function ($q) use ($cutoff) {
                $q->where('check_in_time', '<=', $cutoff);
            });

        $count = 0;
        $query->chunkById(200, function ($visits) use (&$count, $now, $maxHours) {
            foreach ($visits as $visit) {
                $checkIn = $visit->check_in_time ? Carbon::parse($visit->check_in_time) : $now;
                // Auto check-out at min(now, check_in + maxHours)
                $autoOut = min($now->timestamp, $checkIn->copy()->addHours($maxHours)->timestamp);
                $autoOut = Carbon::createFromTimestamp($autoOut);

                // Guard: out must be >= in
                if ($autoOut->lt($checkIn)) {
                    $autoOut = $checkIn;
                }

                $durationHours = max(0, $checkIn->floatDiffInRealHours($autoOut));
                $hourlyRate = optional($visit->staff)->hourly_rate ?? 0;
                $earned = round($durationHours * (float) $hourlyRate, 2);

                $visit->update([
                    'check_out_time' => $autoOut,
                    'status' => 'Completed',
                    'cost' => $earned,
                ]);

                Log::info('Auto checkout applied', [
                    'visit_id' => $visit->id,
                    'check_in' => (string) $checkIn,
                    'check_out' => (string) $autoOut,
                    'hours' => $durationHours,
                    'earned' => $earned,
                ]);

                $count++;
            }
        });

        $this->info("Auto checked out {$count} visits (> {$maxHours}h)");

        return self::SUCCESS;
    }
}

