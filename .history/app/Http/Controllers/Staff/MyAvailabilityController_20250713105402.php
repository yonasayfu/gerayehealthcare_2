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

   public function getEvents(Request $request)
    {
        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
        ]);

        $staffId = Auth::user()->staff->id;
        $appTimezone = config('app.timezone');

        $availabilities = StaffAvailability::where('staff_id', $staffId)
            ->where('start_time', '>=', $request->start)
            ->where('end_time', '<=', $request->end)
            ->get();

        $availabilityEvents = $availabilities->map(function ($availability) use ($appTimezone) {
            
            // We will calculate the converted time and then "dump and die"
            // to see its value immediately.
            $convertedStartTime = Carbon::parse($availability->start_time, 'UTC')
                                      ->setTimezone($appTimezone)
                                      ->toDateTimeString();

            // --- THIS IS THE DEBUGGING LINE ---
            dd($convertedStartTime);

            // The code below will not be reached.
            return [
                'id' => $availability->id,
                'title' => $availability->status,
                'start' => $convertedStartTime,
                'end' => Carbon::parse($availability->end_time, 'UTC')->setTimezone($appTimezone)->toDateTimeString(),
            ];
        });

        // This part of the function will not be reached.
        $visits = ... ;
        $visitEvents = ... ;
        return response()->json($availabilityEvents->concat($visitEvents));
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