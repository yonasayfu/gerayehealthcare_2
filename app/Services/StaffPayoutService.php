<?php

namespace App\Services;

use App\Models\Staff;
use App\Models\StaffPayout;
use App\Models\VisitService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StaffPayoutService
{
    public function getStaffEarningsData(): array
    {
        $staffWithUnpaidEarnings = Staff::withCount([
            'visitServices as unpaid_visits_count' => fn($q) => $q->where('is_paid_to_staff', false)->where('status', 'Completed'),
            'visitServices as unique_patients_count' => fn($q) => $q->where('is_paid_to_staff', false)->where('status', 'Completed')->select(DB::raw('count(distinct(patient_id))')),
        ])
        ->withSum(['visitServices as total_unpaid_cost' => fn($q) => $q->where('is_paid_to_staff', false)->where('status', 'Completed')], 'cost')
        ->orderBy('first_name')
        ->get()
        ->map(function ($staff) {
            $staff->total_hours_logged = $staff->unpaid_visits_count;
            return $staff;
        });

        $staffWithTotalPayouts = Staff::has('payouts')
            ->withSum('payouts', 'total_amount')
            ->get();

        return [
            'staffWithEarnings' => $staffWithUnpaidEarnings,
            'performanceData' => $staffWithTotalPayouts,
        ];
    }

    public function processPayout(int $staffId): void
    {
        $unpaidVisits = VisitService::where('staff_id', $staffId)
            ->where('is_paid_to_staff', false)
            ->where('status', 'Completed')
            ->get();

        if ($unpaidVisits->isEmpty()) {
            throw new \Exception('This staff member has no unpaid visits to process.');
        }

        $totalAmount = $unpaidVisits->sum('cost');

        DB::transaction(function () use ($staffId, $unpaidVisits, $totalAmount) {
            $payout = StaffPayout::create([
                'staff_id' => $staffId,
                'total_amount' => $totalAmount,
                'payout_date' => Carbon::today(),
                'notes' => 'Monthly Payout',
            ]);

            $payout->visitServices()->attach($unpaidVisits->pluck('id'));

            VisitService::whereIn('id', $unpaidVisits->pluck('id'))->update([
                'is_paid_to_staff' => true,
            ]);
        });
    }
}
