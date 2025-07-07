<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\CaregiverAssignment;
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
            abort(403, 'You do not have a staff profile.');
        }
        return Inertia::render('Staff/MyAvailability/Index', ['staff' => $staff]);
    }

    /**
     * Fetch a unified list of events (availabilities AND assignments) for the calendar.
     */
    public function getEvents(Request $request)
    {
        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
        ]);

        $staff = Auth::user()->staff;

        // 1. Fetch self-declared availability
        $availabilities = $staff->availabilities()
            ->where('start_time', '>=', $request->start)
            ->where('end_time', '<=', $request->end)
            ->get();

        $availabilityEvents = $availabilities->map(function ($availability) {
            return [
                'id' => 'avail_' . $availability->id, // Unique prefix
                'title' => $availability->status,
                'start' => $availability->start_time->toIso8601String(),
                'end' => $availability->end_time->toIso8601String(),
                'backgroundColor' => $availability->status === 'Available' ? '#28a745' : '#dc3545',
                'borderColor' => $availability->status === 'Available' ? '#28a745' : '#dc3545',
                'editable' => true, // Staff can edit their own availability
            ];
        });

        // 2. Fetch official assignments
        $assignments = CaregiverAssignment::with('patient')
            ->where('staff_id', $staff->id)
            ->where('shift_start', '>=', $request->start)
            ->where('shift_end', '<=', $request->end)
            ->get();

        $assignmentEvents = $assignments->map(function ($assignment) {
            return [
                'id' => 'assign_' . $assignment->id, // Unique prefix
                'title' => 'Shift: ' . $assignment->patient->full_name,
                'start' => $assignment->shift_start->toIso8601String(),
                'end' => $assignment->shift_end->toIso8601String(),
                'backgroundColor' => '#007bff', // A distinct color for assignments
                'borderColor' => '#007bff',
                'editable' => false, // Staff CANNOT edit official assignments from this calendar
            ];
        });

        // 3. Merge the two collections and return as a single JSON response
        $allEvents = $availabilityEvents->merge($assignmentEvents);

        return response()->json($allEvents);
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

        Auth::user()->staff->availabilities()->create($request->all());

        return response()->json(['message' => 'Availability created.'], 201);
    }

    /**
     * Update an existing availability slot for the authenticated staff member.
     */
    public function update(Request $request, StaffAvailability $availability)
    {
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
        if ($availability->staff_id !== Auth::user()->staff->id) {
            abort(403, 'Unauthorized action.');
        }

        $availability->delete();

        return response()->json(null, 204);
    }
}
