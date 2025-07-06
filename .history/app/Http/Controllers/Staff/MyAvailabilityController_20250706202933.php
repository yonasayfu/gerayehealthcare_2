<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\StaffAvailability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MyAvailabilityController extends Controller
{
    /**
     * Display the staff member's availability calendar.
     */
    public function index()
    {
        $staff = Auth::user()->staff;

        if (!$staff) {
            // Handle case where user is not linked to a staff profile
            abort(403, 'You do not have a staff profile.');
        }

        return Inertia::render('Staff/MyAvailability/Index', [
            'staff' => $staff,
        ]);
    }

    /**
     * Store a new availability slot for the authenticated staff member.
     */
    public function store(Request $request)
    {
        $request->validate([
            'start_time' => 'required|date',
            'end_time' => 'required|date|after_or_equal:start_time',
            'status' => 'required|string|in:Available,Unavailable',
        ]);

        $staff = Auth::user()->staff;

        $staff->availabilities()->create([
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => $request->status,
        ]);

        return response()->json(['message' => 'Availability created.'], 201);
    }

    /**
     * Update an existing availability slot for the authenticated staff member.
     */
    public function update(Request $request, StaffAvailability $availability)
    {
        // Security Check: Ensure the user is updating their OWN availability
        if ($availability->staff_id !== Auth::user()->staff->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'start_time' => 'required|date',
            'end_time' => 'required|date|after_or_equal:start_time',
            'status' => 'required|string|in:Available,Unavailable',
        ]);

        $availability->update($request->only(['start_time', 'end_time', 'status']));

        return response()->json(['message' => 'Availability updated.']);
    }

    /**
     * Delete an availability slot for the authenticated staff member.
     */
    public function destroy(StaffAvailability $availability)
    {
        // Security Check: Ensure the user is deleting their OWN availability
        if ($availability->staff_id !== Auth::user()->staff->id) {
            abort(403, 'Unauthorized action.');
        }

        $availability->delete();

        return response()->json(null, 204); // 204 No Content
    }
}
