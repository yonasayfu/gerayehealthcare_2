<?php

namespace App\Services\StaffPayout;

use App\Models\Staff;
use App\Models\StaffPayout;
use App\Models\VisitService;
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
     * @param Staff $staff
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
        $pendingTotal = $staff->visitServices()
            ->where('status', 'Completed')
            ->where('is_paid_to_staff', false)
            ->sum('cost');

        return [
            'payoutHistory' => $payoutHistory,
            'pendingVisits' => $pendingVisits,
            'pendingTotal' => $pendingTotal,
        ];
    }
}
