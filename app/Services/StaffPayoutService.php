<?php

namespace App\Services;

use App\DTOs\CreateStaffPayoutDTO;
use App\Models\Staff;
use App\Models\StaffPayout;
use App\Models\VisitService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\BaseService;

class StaffPayoutService extends BaseService
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

    public function __construct(StaffPayout $staffPayout)
    {
        parent::__construct($staffPayout);
    }

    public function processPayout(CreateStaffPayoutDTO $dto): void
    {
        $unpaidVisits = VisitService::where('staff_id', $dto->staff_id)
            ->where('is_paid_to_staff', false)
            ->where('status', 'Completed')
            ->get();

        if ($unpaidVisits->isEmpty()) {
            throw new \Exception('This staff member has no unpaid visits to process.');
        }

        $totalAmount = $unpaidVisits->sum('cost');

        DB::transaction(function () use ($dto, $unpaidVisits, $totalAmount) {
            $payout = parent::create([
                'staff_id' => $dto->staff_id,
                'total_amount' => $totalAmount,
                'payout_date' => Carbon::today(),
                'notes' => $dto->notes ?? 'Monthly Payout',
            ]);

            $payout->visitServices()->attach($unpaidVisits->pluck('id'));

            VisitService::whereIn('id', $unpaidVisits->pluck('id'))->update([
                'is_paid_to_staff' => true,
            ]);
        });
    }
}
