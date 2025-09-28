<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\StaffAvailability;
use App\Models\VisitService;
use App\Services\StaffAvailability\StaffAvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class MyAvailabilityController extends Controller
{
    protected $staffAvailabilityService;

    public function __construct(StaffAvailabilityService $staffAvailabilityService)
    {
        $this->staffAvailabilityService = $staffAvailabilityService;
    }

    /**
     * Display the calendar view for the authenticated staff member.
     */
    public function index(Request $request)
    {
        return inertia('Staff/MyAvailability/Index');
    }

    /** Create a new availability slot for the authenticated staff (UTC-safe). */
    public function store(Request $request)
    {
        $staff = Auth::user()->staff;
        if (! $staff) {
            return back()->with('banner', 'Your account is not linked to a staff profile.').
                with('bannerStyle', 'danger');
        }
        $request->validate([
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'status' => 'required|in:Available,Unavailable',
            'timezone' => 'nullable|string',
        ]);

        $tz = $request->input('timezone', config('app.timezone'));
        // Normalize to application timezone (Africa/Addis_Ababa) to avoid 3h jumps
        $startLocal = Carbon::parse($request->start_time, $tz)->clone()->setTimezone(config('app.timezone'));
        $endLocal = Carbon::parse($request->end_time, $tz)->clone()->setTimezone(config('app.timezone'));

        $this->staffAvailabilityService->create([
            'staff_id' => $staff->id,
            'start_time' => $startLocal->toDateTimeString(),
            'end_time' => $endLocal->toDateTimeString(),
            'status' => $request->status,
        ]);

        return back()->with('banner', 'Availability saved.')->with('bannerStyle', 'success');
    }

    /** Update an existing availability slot (UTC-safe). */
    public function update(Request $request, StaffAvailability $availability)
    {
        $staff = Auth::user()->staff;
        if (! $staff) {
            return back()->with('banner', 'Your account is not linked to a staff profile.').
                with('bannerStyle', 'danger');
        }
        abort_unless($availability->staff_id === $staff->id, 403);

        $request->validate([
            'start_time' => 'sometimes|date',
            'end_time' => 'sometimes|date|after:start_time',
            'status' => 'sometimes|in:Available,Unavailable',
            'timezone' => 'nullable|string',
        ]);

        $tz = $request->input('timezone', config('app.timezone'));
        $data = $request->only(['status']);
        if ($request->filled('start_time')) {
            $data['start_time'] = Carbon::parse($request->start_time, $tz)->clone()->setTimezone(config('app.timezone'))->toDateTimeString();
        }
        if ($request->filled('end_time')) {
            $data['end_time'] = Carbon::parse($request->end_time, $tz)->clone()->setTimezone(config('app.timezone'))->toDateTimeString();
        }

        $this->staffAvailabilityService->update($availability->id, array_merge($data, [
            'staff_id' => $availability->staff_id,
        ]));

        return back();
    }

    /** Delete an availability slot. */
    public function destroy(StaffAvailability $availability)
    {
        $staff = Auth::user()->staff;
        if (! $staff) {
            return back()->with('banner', 'Your account is not linked to a staff profile.').
                with('bannerStyle', 'danger');
        }
        abort_unless($availability->staff_id === $staff->id, 403);
        $availability->delete();
        return back();
    }

    /**
     * Handle the request for calendar events.
     */
    public function getEvents(Request $request)
    {
        $staff = Auth::user()->staff;
        if (! $staff) {
            return response()->json([], 403);
        }
        $staffId = $staff->id;

        // Get personal availability slots
        $availabilities = StaffAvailability::where('staff_id', $staffId)
            ->where('start_time', '>=', $request->start)
            ->where('end_time', '<=', $request->end)
            ->get();

        $availabilityEvents = $availabilities->map(function ($availability) {
            // Use the casted Carbon instances as-is (app timezone) and include offset in ISO string
            $start = $availability->start_time instanceof Carbon ? $availability->start_time : Carbon::parse($availability->start_time);
            $end = $availability->end_time instanceof Carbon ? $availability->end_time : Carbon::parse($availability->end_time);
            return [
                'id' => $availability->id,
                'title' => $availability->status,
                'start' => $start->toIso8601String(), // e.g., 2025-09-26T06:30:00+03:00
                'end' => $end->toIso8601String(),
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

        $visitEvents = $visits->map(function ($visit) {
            // Treat scheduled_at as application timezone (Africa/Addis_Ababa) to avoid +3h jump
            $startTime = $visit->scheduled_at instanceof Carbon ? $visit->scheduled_at : Carbon::parse($visit->scheduled_at);
            $endTime = $startTime->copy()->addHour();

            return [
                'id' => 'visit_' . $visit->id,
                'title' => 'Visit: ' . $visit->patient->full_name,
                'start' => $startTime->toIso8601String(),
                'end' => $endTime->toIso8601String(),
                'backgroundColor' => '#0284c7',
                'borderColor' => '#0284c7',
                'extendedProps' => ['is_editable' => false],
            ];
        });

        return response()->json($availabilityEvents->concat($visitEvents));
    }

    /**
     * Lightweight API to check if the current staff has visits within a window.
     */
    public function visitConflicts(Request $request)
    {
        $request->validate([
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'timezone' => 'nullable|string',
        ]);

        $staff = Auth::user()->staff;
        if (! $staff) {
            return response()->json(['hasConflicts' => false, 'count' => 0], 403);
        }
        $staffId = $staff->id;

        // Convert provided times to application timezone for comparison
        $tz = $request->input('timezone', config('app.timezone'));
        $startLocal = Carbon::parse($request->start_time, $tz)->setTimezone(config('app.timezone'));
        $endLocal = Carbon::parse($request->end_time, $tz)->setTimezone(config('app.timezone'));

        $count = VisitService::where('staff_id', $staffId)
            ->where('status', '!=', 'Cancelled')
            ->whereBetween('scheduled_at', [$startLocal->toDateTimeString(), $endLocal->toDateTimeString()])
            ->count();

        return response()->json([
            'hasConflicts' => $count > 0,
            'count' => $count,
        ]);
    }
}
