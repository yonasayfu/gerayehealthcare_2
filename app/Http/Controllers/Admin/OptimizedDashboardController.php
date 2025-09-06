<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Staff;
use App\Models\VisitService;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class OptimizedDashboardController extends Controller
{
    /**
     * Display the optimized admin dashboard with cached stats and optimized queries.
     */
    public function index(): Response
    {
        // Cache dashboard stats for 5 minutes
        $stats = Cache::remember('admin_dashboard_stats', 300, function () {
            return [
                'totalPatients' => Patient::count(),
                'totalStaff' => Staff::where('status', 'Active')->count(),
                'pendingVisits' => VisitService::where('status', 'Pending')->count(),
                'completedVisitsToday' => VisitService::where('status', 'Completed')
                    ->whereDate('created_at', today())
                    ->count(),
                'totalEarnings' => VisitService::where('status', 'Completed')->sum('cost'),
                'unpaidInvoices' => Invoice::where('status', 'Pending')->count(),
                'overdueInvoices' => Invoice::where('status', 'Pending')
                    ->where('due_date', '<', now())
                    ->count(),
            ];
        });

        // Get recent activity with optimized queries (limit and eager load)
        $recentActivity = Cache::remember('admin_dashboard_recent', 180, function () {
            return [
                'recentVisits' => VisitService::with(['patient:id,full_name,phone_number', 'staff:id,first_name,last_name'])
                    ->latest()
                    ->limit(5)
                    ->get(),
                'recentPatients' => Patient::select('id', 'full_name', 'phone_number', 'created_at')
                    ->latest()
                    ->limit(5)
                    ->get(),
                'upcomingVisits' => VisitService::with(['patient:id,full_name,phone_number', 'staff:id,first_name,last_name'])
                    ->where('status', 'Pending')
                    ->where('scheduled_at', '>=', now())
                    ->where('scheduled_at', '<=', now()->addDays(7))
                    ->orderBy('scheduled_at')
                    ->limit(10)
                    ->get(),
            ];
        });

        // Get chart data for dashboard graphs
        $chartData = Cache::remember('admin_dashboard_charts', 600, function () {
            $last30Days = collect(range(29, 0))->map(function ($daysAgo) {
                $date = now()->subDays($daysAgo);

                return [
                    'date' => $date->format('Y-m-d'),
                    'patients' => Patient::whereDate('created_at', $date)->count(),
                    'visits' => VisitService::whereDate('scheduled_at', $date)->count(),
                    'revenue' => VisitService::where('status', 'Completed')
                        ->whereDate('scheduled_at', $date)
                        ->sum('cost'),
                ];
            });

            return [
                'dailyStats' => $last30Days,
                'patientsBySource' => Patient::select('source')
                    ->selectRaw('count(*) as count')
                    ->whereNotNull('source')
                    ->groupBy('source')
                    ->orderBy('count', 'desc')
                    ->limit(10)
                    ->get(),
                'visitsByStatus' => VisitService::select('status')
                    ->selectRaw('count(*) as count')
                    ->groupBy('status')
                    ->get(),
            ];
        });

        return Inertia::render('Admin/Dashboard/Index', [
            'stats' => [
                'totalPatients' => $stats['totalPatients'],
                'totalStaff' => $stats['totalStaff'],
                'pendingVisits' => $stats['pendingVisits'],
                'completedVisitsToday' => $stats['completedVisitsToday'],
                'totalEarnings' => number_format($stats['totalEarnings'], 2),
                'unpaidInvoices' => $stats['unpaidInvoices'],
                'overdueInvoices' => $stats['overdueInvoices'],
            ],
            'recentVisits' => $recentActivity['recentVisits'],
            'recentPatients' => $recentActivity['recentPatients'],
            'upcomingVisits' => $recentActivity['upcomingVisits'],
            'chartData' => $chartData,
        ]);
    }

    /**
     * Clear dashboard cache (for admin use)
     */
    public function clearCache()
    {
        Cache::forget('admin_dashboard_stats');
        Cache::forget('admin_dashboard_recent');
        Cache::forget('admin_dashboard_charts');

        return response()->json(['message' => 'Dashboard cache cleared successfully']);
    }
}
