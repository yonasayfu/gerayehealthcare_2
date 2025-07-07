<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\VisitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;

class MyVisitController extends Controller
{
    /**
     * Display a listing of the visits assigned to the authenticated staff member.
     */
    public function index()
    {
        $staff = Auth::user()->staff;

        if (!$staff) {
            // Handle case where user is not linked to a staff profile
            return redirect()->route('dashboard')->with('error', 'You do not have a staff profile.');
        }

        $visits = VisitService::with('patient')
            ->where('staff_id', $staff->id)
            ->orderBy('scheduled_at', 'asc')
            ->paginate(10);

        return Inertia::render('Staff/MyVisits/Index', [
            'visits' => $visits,
        ]);
    }

    /**
     * Handle the check-in process for a visit.
     */
    public function checkIn(Request $request, VisitService $visit)
    {
        // Authorization: Ensure the visit belongs to the authenticated staff
        if ($visit->staff_id !== Auth::user()->staff->id) {
            return back()->with('error', 'You are not authorized to check in for this visit.');
        }

        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $visit->update([
            'check_in_time' => Carbon::now(),
            'check_in_latitude' => $validated['latitude'],
            'check_in_longitude' => $validated['longitude'],
            'status' => 'In Progress',
        ]);

        return back()->with('success', 'Checked in successfully.');
    }

    /**
     * Handle the check-out process for a visit.
     */
    public function checkOut(Request $request, VisitService $visit)
    {
        // Authorization: Ensure the visit belongs to the authenticated staff
        if ($visit->staff_id !== Auth::user()->staff->id) {
            return back()->with('error', 'You are not authorized to check out for this visit.');
        }

        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $visit->update([
            'check_out_time' => Carbon::now(),
            'check_out_latitude' => $validated['latitude'],
            'check_out_longitude' => $validated['longitude'],
            'status' => 'Completed',
        ]);

        return back()->with('success', 'Checked out successfully.');
    }
}