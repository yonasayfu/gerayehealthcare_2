<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StaffPayoutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Admin/StaffPayouts/Index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Logic to store staff payout
    ])
    ->withSum(['visitServices as total_unpaid_cost' => fn($q) => $q->where('is_paid_to_staff', false)->where('status', 'Completed')], 'cost')
    ->orderBy('first_name')
    ->get()
    ->map(function ($staff) {
        $staff->total_hours_logged = $staff->unpaid_visits_count;
        return $staff;
    });

    // This NEW query gets the TOTAL PAID earnings for the performance chart
    $staffWithTotalPayouts = Staff::has('payouts') // Only get staff who have been paid at least once
        ->withSum('payouts', 'total_amount')
        ->get();

    return Inertia::render('Admin/StaffPayouts/Index', [
        'staffWithEarnings' => $staffWithUnpaidEarnings,
        'performanceData' => $staffWithTotalPayouts, // Pass new data to the view
    ]);
}
    /**
     * Store a new payout for a specific staff member.
     */
    public function store(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:staff,id',
        ]);

        $staffId = $request->input('staff_id');

        // Find all completed visits for this staff member that have not been paid out yet.
        $unpaidVisits = VisitService::where('staff_id', $staffId)
            ->where('is_paid_to_staff', false)
            ->where('status', 'Completed')
            ->get();

        if ($unpaidVisits->isEmpty()) {
            return back()->with('error', 'This staff member has no unpaid visits to process.');
        }

        // Calculate the total payout amount.
        $totalAmount = $unpaidVisits->sum('cost');

        // Use a database transaction to ensure data integrity.
        DB::transaction(function () use ($staffId, $unpaidVisits, $totalAmount) {
            // 1. Create the master payout record.
            $payout = StaffPayout::create([
                'staff_id' => $staffId,
                'total_amount' => $totalAmount,
                'payout_date' => Carbon::today(),
                'notes' => 'Monthly Payout',
            ]);

            // 2. Attach all the unpaid visits to this payout record.
            $payout->visitServices()->attach($unpaidVisits->pluck('id'));

            // 3. Mark all the included visits as paid.
            VisitService::whereIn('id', $unpaidVisits->pluck('id'))->update([
                'is_paid_to_staff' => true,
            ]);
        });

        return back()->with('success', 'Payout processed successfully.');
    }
}