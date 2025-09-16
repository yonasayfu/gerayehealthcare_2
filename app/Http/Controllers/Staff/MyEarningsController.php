<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Services\StaffPayout\StaffPayoutService; // Import the new service
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\StaffPayoutRequested;
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

        if (! $staff) {
            return redirect()->route('dashboard')->with('error', 'You do not have a staff profile.');
        }

        // Delegate data fetching to the service
        $earningsData = $this->staffPayoutService->getStaffEarnings($staff);

        return Inertia::render('Staff/MyEarnings/Index', $earningsData);
    }

    /**
     * Allow a staff member to request a payout for their current pending earnings.
     */
    public function requestPayout(Request $request)
    {
        $staff = auth()->user()->staff;

        if (! $staff) {
            return redirect()->route('dashboard')->with('banner', 'No staff profile found.')->with('bannerStyle', 'danger');
        }

        $notes = $request->input('notes');

        // Snapshot the current pending total (without marking visits as paid)
        $data = $this->staffPayoutService->getStaffEarnings($staff);
        $pendingTotal = (float) ($data['pendingTotal'] ?? 0);

        // Create a placeholder payout record as a request; admin will later process actual payout
        $payout = $this->staffPayoutService->create([
            'staff_id'    => $staff->id,
            'total_amount'=> $pendingTotal,
            'payout_date' => now(),
            'status'      => 'Pending',
            'notes'       => trim('Staff requested payout'.($notes ? ": {$notes}" : '')),
            'requested_by'=> auth()->id(),
        ]);

        // Notify admins (users who can view staff payouts)
        $admins = User::permission('view staff payouts')->get();
        if ($admins->count() > 0) {
            Notification::send($admins, new StaffPayoutRequested(
                payoutId: $payout->id,
                staffName: $staff->full_name,
                amount: (float) $pendingTotal,
                notes: $notes,
            ));
        }

        return back()->with('banner', 'Payout request submitted. Admin will review and process.')->with('bannerStyle', 'success');
    }
}
