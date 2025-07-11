<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Staff;
use App\Models\VisitService;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with key stats and recent activity.
     */
    public function index(): Response
    {
        // 1. Fetch Key Performance Indicators (KPIs)
        $totalPatients = Patient::count();
        $totalStaff = Staff::count();
        $pendingVisits = VisitService::where('status', 'Pending')->count();
        $totalEarnings = VisitService::where('status', 'Completed')->sum('cost');

        // 2. Fetch Recent Activity
        // Eager load patient and staff to prevent N+1 query issues on the frontend
        $recentVisits = VisitService::with(['patient', 'staff'])
            ->latest() // Order by created_at descending
            ->take(5)  // Get the 5 most recent
            ->get();

        // 3. Return the Inertia view with all the data as props
        return Inertia::render('Admin/Dashboard/Index', [
            'stats' => [
                'totalPatients' => $totalPatients,
                'totalStaff' => $totalStaff,
                'pendingVisits' => $pendingVisits,
                'totalEarnings' => number_format($totalEarnings, 2),
            ],
            'recentVisits' => $recentVisits,
        ]);
    }
}