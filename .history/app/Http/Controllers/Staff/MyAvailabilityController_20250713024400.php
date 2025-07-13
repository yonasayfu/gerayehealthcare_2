<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\StaffAvailability;
use App\Models\VisitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    /**
     * Fetch events for the calendar.
     */
    public function getEvents(Request $request)
    {
        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
        ]);

        $staffId = Auth::user()->staff->id;

        // Get personal availability slots
        $availabilities = StaffAvailability::where('staff_id', $staffId)
            ->where('start_time', '>=', $request->start)
            ->where('end_time', '<=', $request->end)
            ->get();

        $availabilityEvents = $availabilities->map(function ($availability) {
            return [
                'id' => $availability->id,
                'title' => $availability->status,
                'start' => $availability->start_time->toIso8601String(),
                'end' => $availability->end_time->toIso8601String(),
                'backgroundColor' => $availability->status === 'Available' ? '#28a745' : '#dc3545',
                'borderColor' => $availability->status === 'Available' ? '#28a745' : '#dc3545',
                'extendedProps' => [
                    'status' => $availability->status,
                    'is_editable' => true,
                ]
            ];
        });

        // Get scheduled work assignments (visits)
        $visits = VisitService::where('staff_id', $staffId)
            ->where('scheduled_at', '>=', $request->start)
            ->where('scheduled_at', '<=', $request->end)
            ->where('status', '!=', 'Cancelled')
            ->with('patient')
            ->get();

        $visitEvents = $visits->map(function ($visit) {
            $endTime = (new \Carbon\Carbon($visit->scheduled_at))->addHour();
            return [
                'id' => 'visit_' . $visit->id,
                'title' => 'Visit: ' . $visit->patient->full_name,
                'start' => $visit->scheduled_at->toIso8601String(),
                'end' => $endTime->toIso8601String(),
                'backgroundColor' => '#0284c7',
                'borderColor' => '#0284c7',
                'extendedProps' => [
                    'is_editable' => false,
                ]
            ];
        });

        return response()->json($availabilityEvents->concat($visitEvents));
    }

    /**
     * Store a new availability slot.
     */
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

    /**
     * Update an existing availability slot.
     */
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

    /**
     * Remove an availability slot.
     */
    public function destroy(StaffAvailability $availability)
    {
        if ($availability->staff_id !== Auth::user()->staff->id) {
            abort(403);
        }

        $availability->delete();

        return back()->with('success', 'Availability deleted.');
    }
}
