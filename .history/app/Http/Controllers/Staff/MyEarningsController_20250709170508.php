<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\VisitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MyEarningsController extends Controller
{
    /**
     * Display the earnings and payout history for the authenticated staff member.
     */
    public function index()
    {
        $staff = Auth::user()->staff;

        if (!$staff) {
            return redirect()->route('dashboard')->with('error', 'You do not have a staff profile.');
        }

        // Fetch the payout history
        $payoutHistory = $staff->payouts()
            ->orderBy('payout_date', 'desc')
            ->paginate(10, ['*'], 'payouts_page');

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

        return Inertia::render('Staff/MyEarnings/Index', [
            'payoutHistory' => $payoutHistory,
            'pendingVisits' => $pendingVisits,
            'pendingTotal' => $pendingTotal,
        ]);
    }
}