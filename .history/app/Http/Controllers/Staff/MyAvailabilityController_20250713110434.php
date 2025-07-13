<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\StaffAvailability;
use App\Models\VisitService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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

    public function getEvents(Request $request)
    {
        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
        ]);

        $staffId = Auth::user()->staff->id;
        $appTimezone = config('app.timezone');

        // Get personal availability slots
        $availabilities = StaffAvailability::where('staff_id', $staffId)
            ->where('start_time', '>=', $request->start)
            ->where('end_time', '<=', $request->end)
            ->get();

        $availabilityEvents = $availabilities->map(function ($availability) use ($appTimezone) {
            // THE FIX: Force the timezone conversion and then format to a simple string
            $startTime = Carbon::parse($availability->start_time)->setTimezone($appTimezone);
            $endTime = Carbon::parse($availability->end_time)->setTimezone($appTimezone);

            return [
                'id' => $availability->id,
                'title' => $availability->status,
                'start' => $startTime->format('Y-m-d H:i:s'),
                'end' => $endTime->format('Y-m-d H:i:s'),
                'backgroundColor' => $availability->status === 'Available' ? '#28a745' : '#dc3545',
                'borderColor' => $availability->status === 'Available' ? '#28a745' : '#dc3545',
                'extendedProps' => ['status' => $availability->status, 'is_editable' => true],
            ];
        });

        // Get scheduled work assignments (visits)
        $visits = VisitService::where('staff_id', $staffId)
            ->where('scheduled_at', '>=', $request->start)
            ->where('scheduled_at', '<=', $request->end)
            ->where('status', '!=', 'Cancelled')
            ->with('patient')
            ->get();

        $visitEvents = $visits->map(function ($visit) use ($appTimezone) {
            $startTime = Carbon::parse($visit->scheduled_at)->setTimezone($appTimezone);
            $endTime = $startTime->copy()->addHour();
            return [
                'id' => 'visit_' . $visit->id,
                'title' => 'Visit: ' . $visit->patient->full_name,
                'start' => $startTime->format('Y-m-d H:i:s'),
                'end' => $endTime->format('Y-m-d H:i:s'),
                'backgroundColor' => '#0284c7',
                'borderColor' => '#0284c7',
                'extendedProps' => ['is_editable' => false],
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
