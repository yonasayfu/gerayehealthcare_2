<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Services\StaffPayout\StaffPayoutService; // Import the new service
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MyEarningsController extends Controller
{
    protected StaffPayoutService $staffPayoutService;

    public function __construct(StaffPayoutService $staffPayoutService)
    {
        $this->staffPayoutService = $staffPayoutService;
    }

    /**
     * Display the earnings and payout history for the authenticated staff member.
     */
    public function index()
    {
        $staff = Auth::user()->staff;

        if (!$staff) {
            return redirect()->route('dashboard')->with('error', 'You do not have a staff profile.');
        }

        // Delegate data fetching to the service
        $earningsData = $this->staffPayoutService->getStaffEarnings($staff);

        return Inertia::render('Staff/MyEarnings/Index', $earningsData);
    }
}
        $pendingTotal = $staff->visitServices()
            ->where('status', 'Completed')
            ->where('is_paid_to_staff', false)
            ->sum('cost');

        return Inertia::render('Staff/MyEarnings/Index', [
            'payoutHistory' => $payoutHistory,
            'pendingVisits' => $pendingVisits,
            'pendingTotal' => $pendingTotal,
        ]);
    }
}