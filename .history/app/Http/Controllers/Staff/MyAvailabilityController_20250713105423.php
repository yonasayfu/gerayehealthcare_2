<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\StaffAvailability;
use App\Models\VisitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon; // Make sure Carbon is imported

class MyAvailabilityController extends Controller
{
    /**
     * Display the calendar view for the authenticated staff member.
     */
    public function index()
    {
        return \Inertia\Inertia::render('Staff/MyAvailability/Index', [
            'staff' => Auth::user()->staff,
        ]);
    }

   
    
    // ... your store(), update(), and destroy() methods remain the same ...
    public function store(Request $request)
    {
        $validated = $request->validate([
            'start_time' => 'required|date',
            'end_time' => 'required|date|after_or_equal:start_time',
            'status' => 'required|string|in:Available,Unavailable',
        ]);

        Auth::user()->staff->availabilities()->create($validated);

        return back()->with('success', 'Availability created.');
    }

    public function update(Request $request, StaffAvailability $availability)
    {
        if ($availability->staff_id !== Auth::user()->staff->id) {
            abort(403);
        }

        $validated = $request->validate([
            'start_time' => 'required|date',
            'end_time' => 'required|date|after_or_equal:start_time',
            'status' => 'required|string|in:Available,Unavailable',
        ]);

        $availability->update($validated);

        return back()->with('success', 'Availability updated.');
    }

    public function destroy(StaffAvailability $availability)
    {
        if ($availability->staff_id !== Auth::user()->staff->id) {
            abort(403);
        }

        $availability->delete();

        return back()->with('success', 'Availability deleted.');
    }
}