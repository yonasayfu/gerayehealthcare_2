<?php

namespace App\Services\StaffPayout;

use App\Models\Staff;
use App\Models\StaffPayout;
use App\Services\BaseService;
use Illuminate\Pagination\LengthAwarePaginator;

class StaffPayoutService extends BaseService
{
    public function __construct(StaffPayout $model)
    {
        parent::__construct($model);
    }

    /**
     * Get payout history and pending visits for a specific staff member.
     *
     * @return array{payoutHistory: LengthAwarePaginator, pendingVisits: LengthAwarePaginator, pendingTotal: float}
     */
    public function getStaffEarnings(Staff $staff): array
    {
        // Fetch the payout history
        $payoutHistory = $staff->payouts()
            ->orderBy('payout_date', 'desc')
            ->paginate(5, ['*'], 'payouts_page');

        // Fetch current work that has not yet been included in a payout
        $pendingVisits = $staff->visitServices()
            ->where('status', 'Completed')
            ->where('is_paid_to_staff', false)
            ->orderBy('scheduled_at', 'desc')
            ->paginate(10, ['*'], 'pending_visits_page');

        // Calculate the total of pending earnings
        // Prefer duration-based earnings (hourly_rate * actual hours) if times exist; fallback to cost
        $pendingVisitsForSum = $staff->visitServices()
            ->where('status', 'Completed')
            ->where('is_paid_to_staff', false)
            ->get(['check_in_time', 'check_out_time']);

        $hours = $pendingVisitsForSum->reduce(function ($carry, $v) {
            if ($v->check_in_time && $v->check_out_time) {
                $start = \Illuminate\Support\Carbon::parse($v->check_in_time);
                $end = \Illuminate\Support\Carbon::parse($v->check_out_time);
                return $carry + max(0, $end->floatDiffInRealHours($start));
            }
            return $carry;
        }, 0.0);

        $pendingTotal = round($hours * ((float) ($staff->hourly_rate ?? 0)), 2);

        // If hours-based sum is zero (e.g., legacy data), fallback to stored cost sum
        if ($pendingTotal <= 0) {
            $pendingTotal = $staff->visitServices()
                ->where('status', 'Completed')
                ->where('is_paid_to_staff', false)
                ->sum('cost');
        }

        // Add optional contextual metric: approved leave days in current month
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        $approvedLeaves = $staff->leaveRequests()
            ->where('status', 'Approved')
            ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('start_date', [$startOfMonth, $endOfMonth])
                  ->orWhereBetween('end_date', [$startOfMonth, $endOfMonth])
                  ->orWhere(function ($qq) use ($startOfMonth, $endOfMonth) {
                      $qq->where('start_date', '<=', $startOfMonth)
                         ->where('end_date', '>=', $endOfMonth);
                  });
            })
            ->get(['start_date', 'end_date']);

        $leaveDaysThisMonth = $approvedLeaves->reduce(function ($carry, $lr) use ($startOfMonth, $endOfMonth) {
            $from = \Illuminate\Support\Carbon::parse($lr->start_date)->max($startOfMonth);
            $to = \Illuminate\Support\Carbon::parse($lr->end_date)->min($endOfMonth);
            return $carry + max(0, $to->diffInDays($from) + 1);
        }, 0);

        return [
            'payoutHistory' => $payoutHistory,
            'pendingVisits' => $pendingVisits,
            'pendingTotal' => $pendingTotal,
            'leaveDaysThisMonth' => $leaveDaysThisMonth,
        ];
    }
}
