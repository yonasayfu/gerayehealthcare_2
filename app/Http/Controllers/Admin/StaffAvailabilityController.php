<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\StaffAvailability;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StaffAvailabilityController extends Controller
{
    /**
     * Fetch availability events for the FullCalendar component.
     * Responds with JSON.
     */
public function getCalendarEvents(Request $request)
    {
        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
        ]);

        $query = \App\Models\StaffAvailability::with('staff')
            ->where('start_time', '>=', $request->start)
            ->where('end_time', '<=', $request->end);

        if ($request->filled('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }

        $availabilities = $query->get();

        $events = $availabilities->map(function ($availability) {
            return [
                'id' => $availability->id,
                'title' => $availability->staff->first_name . ' (' . $availability->status . ')',
                'start' => $availability->start_time->format('Y-m-d H:i:s'),
                'end' => $availability->end_time->format('Y-m-d H:i:s'),
                'backgroundColor' => $availability->status === 'Available' ? '#28a745' : '#dc3545',
                'borderColor' => $availability->status === 'Available' ? '#28a745' : '#dc3545',
                'extendedProps' => [
                    'staff_name' => $availability->staff->first_name . ' ' . $availability->staff->last_name,
                    'status' => $availability->status,
                ]
            ];
        });

        return response()->json($events);
    }

    /**
     * Display a paginated list view for admins.
     * Responds with an Inertia view.
     */
    public function index(Request $request)
    {
        $query = StaffAvailability::with('staff');

        // Filtering logic
        if ($request->filled('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('start_time', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('end_time', '<=', $request->end_date);
        }

        // Sorting logic
        if ($request->filled('sort')) {
            $query->orderBy($request->sort, $request->get('direction', 'asc'));
        } else {
            $query->orderBy('start_time', 'desc');
        }

        return Inertia::render('Admin/StaffAvailabilities/Index', [
            'availabilities' => $query->paginate($request->get('per_page', 15))->withQueryString(),
            'filters' => $request->only(['staff_id', 'status', 'start_date', 'end_date', 'sort', 'direction']),
            'staffList' => Staff::orderBy('first_name')->get(['id', 'first_name', 'last_name']),
        ]);
    }

    /**
     * Store a new availability slot.
     */
public function store(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after_or_equal:start_time',
            'status' => 'required|string|in:Available,Unavailable',
        ]);

        // Check for overlapping availability
        $overlap = StaffAvailability::where('staff_id', $request->staff_id)
            ->where('end_time', '>', $request->start_time)
            ->where('start_time', '<', $request->end_time)
            ->exists();

        if ($overlap) {
            return back()->withErrors(['error' => 'Conflict: Overlapping availability slot exists.'])->withInput();
        }

        $availability = StaffAvailability::create($request->only(['staff_id', 'start_time', 'end_time', 'status']));

        return back()->with('success', 'Availability slot created.');
    }

    /**
     * Update an existing availability slot.
     */
public function update(Request $request, StaffAvailability $availability)
    {
        $request->validate([
            'start_time' => 'required|date',
            'end_time' => 'required|date|after_or_equal:start_time',
            'status' => 'required|string|in:Available,Unavailable',
        ]);

        // Check for overlapping availability
        $overlap = StaffAvailability::where('staff_id', $availability->staff_id)
            ->where('end_time', '>', $request->start_time)
            ->where('start_time', '<', $request->end_time)
            ->where('id', '<>', $availability->id)
            ->exists();

        if ($overlap) {
            return back()->withErrors(['error' => 'Conflict: Overlapping availability slot exists.'])->withInput();
        }

        $availability->update($request->only(['start_time', 'end_time', 'status']));

        return back()->with('success', 'Availability slot updated.');
    }

    /**
     * Remove an availability slot (cancellation).
     */
    public function destroy(StaffAvailability $availability)
    {
        $availability->delete();

        return back()->with('success', 'Availability slot cancelled.');
    }
}
