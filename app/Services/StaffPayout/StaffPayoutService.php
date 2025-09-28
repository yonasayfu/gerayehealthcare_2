<?php

namespace App\Services\StaffPayout;

use App\DTOs\CreateStaffPayoutDTO;
use App\Models\Staff;
use App\Models\StaffPayout;
use App\Models\VisitService;
use App\Services\Base\BaseService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StaffPayoutService extends BaseService
{
    public function getStaffEarningsData(Request $request): array
    {
        $perPage = (int) $request->input('per_page', 5);

        $staffWithUnpaidEarningsQuery = Staff::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->input('search');
                $q->where(function ($w) use ($search) {
                    $w->where('first_name', 'ilike', "%$search%")
                        ->orWhere('last_name', 'ilike', "%$search%");
                });
            })
            ->withCount([
                'visitServices as unpaid_visits_count' => function ($q) {
                    $q->where('status', 'Completed')
                        ->where(function ($r) {
                            $r->where('is_paid_to_staff', false)
                                ->orWhereNull('is_paid_to_staff');
                        });
                },
                'visitServices as unique_patients_count' => function ($q) {
                    $q->where('status', 'Completed')
                        ->where(function ($r) {
                            $r->where('is_paid_to_staff', false)
                                ->orWhereNull('is_paid_to_staff');
                        })
                        ->select(DB::raw('count(distinct(patient_id))'));
                },
            ])
            ->withSum(['visitServices as total_unpaid_cost' => function ($q) {
                $q->where('status', 'Completed')
                    ->where(function ($r) {
                        $r->where('is_paid_to_staff', false)
                            ->orWhereNull('is_paid_to_staff');
                    });
            }], 'cost')
            ->withCount(['payouts as pending_payout_requests_count' => function ($q) {
                $q->where('status', 'Pending');
            }])
            ->with(['payouts' => function ($q) {
                $q->latest()->limit(1);
            }])
            ->addSelect([
                'latest_payout_id' => \App\Models\StaffPayout::select('id')
                    ->whereColumn('staff_id', 'staff.id')
                    ->latest()
                    ->limit(1),
            ])
            ->orderBy('first_name');

        // Sorting
        $sort = $request->input('sort');
        $direction = $request->input('direction', 'asc');
        $allowedSorts = [
            'first_name',
            'unpaid_visits_count',
            'total_unpaid_cost',
            'pending_payout_requests_count',
        ];
        if ($sort && in_array($sort, $allowedSorts, true)) {
            $staffWithUnpaidEarningsQuery->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');
        }

        $staffWithUnpaidEarnings = $staffWithUnpaidEarningsQuery
            ->paginate($perPage)
            ->through(function ($staff) {
                // Compute total hours from check-in/out duration for unpaid completed visits
                $totalSeconds = VisitService::where('staff_id', $staff->id)
                    ->where('status', 'Completed')
                    ->where(function ($r) {
                        $r->where('is_paid_to_staff', false)
                            ->orWhereNull('is_paid_to_staff');
                    })
                    ->whereNotNull('check_in_time')
                    ->whereNotNull('check_out_time')
                    ->get(['check_in_time', 'check_out_time'])
                    ->reduce(function ($carry, $v) {
                        $start = \Illuminate\Support\Carbon::parse($v->check_in_time);
                        $end = \Illuminate\Support\Carbon::parse($v->check_out_time);
                        return $carry + max(0, $end->diffInSeconds($start));
                    }, 0);

                $staff->total_hours_logged = round($totalSeconds / 3600, 2);

                return $staff;
            });

        // Preserve filters in pagination links
        $staffWithUnpaidEarnings->appends($request->only(['per_page', 'search']));

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

    public function processPayout(CreateStaffPayoutDTO $dto): StaffPayout
    {
        $unpaidVisits = VisitService::where('staff_id', $dto->staff_id)
            ->where('is_paid_to_staff', false)
            ->where('status', 'Completed')
            ->get();

        if ($unpaidVisits->isEmpty()) {
            throw new \Exception('This staff member has no unpaid visits to process.');
        }

        $totalAmount = $unpaidVisits->sum('cost');

        return DB::transaction(function () use ($dto, $unpaidVisits, $totalAmount) {
            $payout = parent::create([
                'staff_id' => $dto->staff_id,
                'total_amount' => $totalAmount,
                'payout_date' => Carbon::today(),
                'status' => $dto->status ?? 'Completed',
                'notes' => $dto->notes ?? 'Monthly Payout',
                'processed_by' => Auth::id(),
                'processed_notes' => $dto->notes,
            ]);

            $payout->visitServices()->attach($unpaidVisits->pluck('id'));

            VisitService::whereIn('id', $unpaidVisits->pluck('id'))->update([
                'is_paid_to_staff' => true,
            ]);

            return $payout;
        });
    }
}
