<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\VisitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the dashboard for the currently authenticated staff member.
     */
    public function index(): Response
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $staffId = $user->staff->id;

        // 1. Fetch upcoming visits for this staff member
        $upcomingVisits = VisitService::with('patient')
            ->where('staff_id', $staffId)
            ->where('status', 'Pending')
            ->orderBy('scheduled_at', 'asc')
            ->take(5)
            ->get();

        // 2. Fetch stats about unpaid work
        $unpaidVisitsCount = VisitService::where('staff_id', $staffId)
            ->where('status', 'Completed')
            ->where('is_paid_to_staff', false)
            ->count();

        // To calculate earnings, we use the staff member's own hourly rate, not the patient-facing service cost.
        // This requires calculating the duration of each visit.
        $unpaidVisits = VisitService::where('staff_id', $staffId)
            ->where('status', 'Completed')
            ->where('is_paid_to_staff', false)
            ->whereNotNull(['check_in_time', 'check_out_time'])
            ->get();
        
        $unpaidEarnings = $unpaidVisits->reduce(function ($carry, $visit) {
            $startTime = new \DateTime($visit->check_in_time);
            $endTime = new \DateTime($visit->check_out_time);
            $durationInHours = ($endTime->getTimestamp() - $startTime->getTimestamp()) / 3600;
            $earningsForVisit = $durationInHours * Auth::user()->staff->hourly_rate;
            return $carry + $earningsForVisit;
        }, 0);


        // 3. Return the Inertia view with the data
        return Inertia::render('Dashboard', [
            'stats' => [
                'upcomingVisitsCount' => $upcomingVisits->count(),
                'unpaidVisitsCount' => $unpaidVisitsCount,
                'unpaidEarnings' => number_format($unpaidEarnings, 2),
            ],
            'upcomingVisits' => $upcomingVisits,
        ]);
    }
}