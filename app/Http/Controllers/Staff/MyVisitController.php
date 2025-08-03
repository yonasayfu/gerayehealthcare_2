<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Service; // <-- THIS IS THE FIX
use App\Models\VisitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;
use App\Http\Requests\CheckInVisitRequest;
use App\Http\Requests\CheckOutVisitRequest;
use App\Http\Requests\StoreVisitReportRequest;

class MyVisitController extends Controller
{
    /**
     * Display a listing of the visits assigned to the authenticated staff member.
     */
    public function index()
    {
        $staff = Auth::user()->staff;

        if (!$staff) {
            return redirect()->route('dashboard')->with('error', 'You do not have a staff profile.');
        }

        $visits = VisitService::with('patient')
            ->where('staff_id', $staff->id)
            ->orderBy('scheduled_at', 'desc')
            ->paginate(10);

        return Inertia::render('Staff/MyVisits/Index', [
            'visits' => $visits,
        ]);
    }

    /**
     * Handle the check-in process for a visit.
     */
    public function checkIn(CheckInVisitRequest $request, VisitService $visit)
    {
        if ($visit->staff_id !== Auth::user()->staff->id) {
            abort(403);
        }

        $validated = $request->validated();

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
    public function checkOut(CheckOutVisitRequest $request, VisitService $visit)
    {
        if ($visit->staff_id !== Auth::user()->staff->id) {
            abort(403);
        }

        $validated = $request->validated();

        $visit->update([
            'check_out_time' => Carbon::now(),
            'check_out_latitude' => $validated['latitude'],
            'check_out_longitude' => $validated['longitude'],
            'status' => 'Completed',
        ]);

        return back()->with('success', 'Checked out successfully.');
    }

    /**
     * Show the form for filing a post-visit report.
     */
    public function showReportForm(VisitService $visit)
    {
        // Security check
        if ($visit->staff_id !== Auth::user()->staff->id) {
            abort(403);
        }

        return Inertia::render('Staff/MyVisits/FileReport', [
            'visit' => $visit->load('patient'),
            'services' => Service::where('is_active', true)->orderBy('name')->get(['id', 'name', 'price']),
        ]);
    }

    /**
     * Store the post-visit report.
     */
    public function storeReport(StoreVisitReportRequest $request, VisitService $visit)
    {
        // Security check
        if ($visit->staff_id !== Auth::user()->staff->id) {
            abort(403);
        }

        $validated = $request->validated();

        // Find the selected service to get its price
        $service = Service::find($validated['service_id']);

        $visit->update([
            'service_id' => $validated['service_id'],
            'visit_notes' => $validated['visit_notes'],
            'cost' => $service->price, // Update the visit cost based on the selected service's price
        ]);

        return redirect()->route('staff.my-visits.index')->with('success', 'Visit report filed successfully.');
    }
}
