<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Staff;
use App\Models\VisitService;
use App\Models\Invoice;
use App\Models\InsuranceClaim;
use App\Models\InventoryAlert;
use App\Models\TaskDelegation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\JsonResponse;
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

    /**
     * Return aggregated Super Admin Overview KPIs as JSON.
     * Uses short caching to keep the dashboard responsive without heavy queries.
     */
    public function overviewData(): JsonResponse
    {
        $data = Cache::remember('dashboard_overview_kpis', 60, function () {
            $now = Carbon::now();
            $startOfMonth = $now->copy()->startOfMonth();

            $totalPatients = Patient::count();
            $newPatientsThisMonth = Patient::whereBetween('created_at', [$startOfMonth, $now])->count();

            $visitsToday = VisitService::whereDate('scheduled_at', $now->toDateString())->count();
            $serviceVolumeThisMonth = VisitService::whereBetween('scheduled_at', [$startOfMonth, $now])->count();

            // Revenue and AR from invoices (prefer grand_total, fallback to amount)
            $sumGrandPaid = Invoice::whereNotNull('paid_at')->sum('grand_total');
            $sumAmountPaid = Invoice::whereNotNull('paid_at')->sum('amount');
            $totalRevenue = $sumGrandPaid ?: $sumAmountPaid;

            $sumGrandUnpaid = Invoice::whereNull('paid_at')->sum('grand_total');
            $sumAmountUnpaid = Invoice::whereNull('paid_at')->sum('amount');
            $accountsReceivable = $sumGrandUnpaid ?: $sumAmountUnpaid;

            // Claims pending: consider claims without payment received as pending
            $claimsPending = InsuranceClaim::whereNull('payment_received_at')->count();

            $activeStaff = Staff::count();
            $lowStockAlerts = InventoryAlert::where('is_active', true)->count();
            $openTasks = TaskDelegation::where('status', '!=', 'Completed')->count();

            return [
                'totalPatients' => $totalPatients,
                'newPatientsThisMonth' => $newPatientsThisMonth,
                'visitsToday' => $visitsToday,
                'serviceVolumeThisMonth' => $serviceVolumeThisMonth,
                'totalRevenue' => (float) number_format((float) $totalRevenue, 2, '.', ''),
                'accountsReceivable' => (float) number_format((float) $accountsReceivable, 2, '.', ''),
                'claimsPending' => $claimsPending,
                'activeStaff' => $activeStaff,
                'lowStockAlerts' => $lowStockAlerts,
                'openTasks' => $openTasks,
            ];
        });

        return response()->json($data);
    }

    /**
     * Return Overview time series for current month: daily patient registrations and visits scheduled.
     */
    public function overviewSeries(): JsonResponse
    {
        $data = Cache::remember('dashboard_overview_series', 60, function () {
            $now = Carbon::now();
            $startOfMonth = $now->copy()->startOfMonth();
            $daysInMonth = $now->daysInMonth;

            $labels = [];
            $registrations = array_fill(0, $daysInMonth, 0);
            $visits = array_fill(0, $daysInMonth, 0);

            // Aggregate patients by day
            Patient::whereBetween('created_at', [$startOfMonth, $now])
                ->get(['created_at'])
                ->each(function ($p) use (&$registrations, $startOfMonth) {
                    $index = $p->created_at->diffInDays($startOfMonth);
                    if (isset($registrations[$index])) {
                        $registrations[$index] += 1;
                    }
                });

            // Aggregate visits by day
            VisitService::whereBetween('scheduled_at', [$startOfMonth, $now])
                ->get(['scheduled_at'])
                ->each(function ($v) use (&$visits, $startOfMonth) {
                    $index = $v->scheduled_at->diffInDays($startOfMonth);
                    if (isset($visits[$index])) {
                        $visits[$index] += 1;
                    }
                });

            for ($d = 1; $d <= $daysInMonth; $d++) {
                $labels[] = $startOfMonth->copy()->addDays($d - 1)->format('M d');
            }

            return [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Registrations',
                        'backgroundColor' => '#3b82f6',
                        'data' => $registrations,
                    ],
                    [
                        'label' => 'Visits',
                        'backgroundColor' => '#66BB6A',
                        'data' => $visits,
                    ],
                ],
            ];
        });

        return response()->json($data);
    }
}