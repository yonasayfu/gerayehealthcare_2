<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InsuranceClaim;
use App\Models\InventoryAlert;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Staff;
use App\Models\TaskDelegation;
use App\Models\VisitService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
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
        $noCache = request()->boolean('noCache');
        $startParam = request('start');
        $endParam = request('end');
        $now = Carbon::now();
        $start = $startParam ? Carbon::parse($startParam) : $now->copy()->startOfMonth();
        $end = $endParam ? Carbon::parse($endParam)->endOfDay() : $now->copy()->endOfDay();

        $cacheKey = 'dashboard_overview_kpis_'.$start->toDateString().'_'.$end->toDateString();

        $compute = function () use ($start, $end, $now) {

            $totalPatients = Patient::count();
            $patientsInRange = Patient::whereBetween('created_at', [$start->copy()->startOfDay(), $end])->count();

            $visitsToday = VisitService::whereBetween('scheduled_at', [$now->copy()->startOfDay(), $now->copy()->endOfDay()])->count();
            $serviceVolumeThisMonth = VisitService::whereBetween('scheduled_at', [$start, $end])->count();

            // Revenue and AR from invoices (prefer grand_total, fallback to amount)
            $sumGrandPaid = Invoice::whereNotNull('paid_at')->whereBetween('created_at', [$start, $end])->sum('grand_total');
            $sumAmountPaid = Invoice::whereNotNull('paid_at')->whereBetween('created_at', [$start, $end])->sum('amount');
            $totalRevenue = $sumGrandPaid ?: $sumAmountPaid;

            $sumGrandUnpaid = Invoice::whereNull('paid_at')->whereBetween('created_at', [$start, $end])->sum('grand_total');
            $sumAmountUnpaid = Invoice::whereNull('paid_at')->whereBetween('created_at', [$start, $end])->sum('amount');
            $accountsReceivable = $sumGrandUnpaid ?: $sumAmountUnpaid;

            // Claims pending: consider claims without payment received as pending
            $claimsPending = InsuranceClaim::whereNull('payment_received_at')->whereBetween('created_at', [$start, $end])->count();

            $activeStaff = Staff::count();
            $lowStockAlerts = InventoryAlert::where('is_active', true)->count();
            $openTasks = TaskDelegation::where('status', '!=', 'Completed')->count();

            // Simple AR aging buckets by invoice created_at age (0–30, 31–60, 61+)
            $ar0to30 = Invoice::whereNull('paid_at')->where('created_at', '>=', now()->subDays(30))->count();
            $ar31to60 = Invoice::whereNull('paid_at')->whereBetween('created_at', [now()->subDays(60), now()->subDays(31)])->count();
            $ar61plus = Invoice::whereNull('paid_at')->where('created_at', '<', now()->subDays(60))->count();

            // Upcoming visits count (next 24h) for quick action
            $upcomingVisitsCount = VisitService::whereBetween('scheduled_at', [$now->copy(), $now->copy()->addDay()->endOfDay()])->count();

            return [
                'totalPatients' => $totalPatients,
                'newPatientsThisMonth' => $patientsInRange, // keep key for backward compatibility
                'patientsInRange' => $patientsInRange,
                'visitsToday' => $visitsToday,
                'serviceVolumeThisMonth' => $serviceVolumeThisMonth,
                'totalRevenue' => (float) number_format((float) $totalRevenue, 2, '.', ''),
                'accountsReceivable' => (float) number_format((float) $accountsReceivable, 2, '.', ''),
                'claimsPending' => $claimsPending,
                'activeStaff' => $activeStaff,
                'lowStockAlerts' => $lowStockAlerts,
                'openTasks' => $openTasks,
                'upcomingVisitsCount' => $upcomingVisitsCount,
                'arAging' => [
                    '0_30' => $ar0to30,
                    '31_60' => $ar31to60,
                    '61_plus' => $ar61plus,
                ],
            ];
        };

        $data = $noCache ? $compute() : Cache::remember($cacheKey, 60, $compute);

        return response()->json($data);
    }

    /**
     * Return Overview time series for current month: daily patient registrations and visits scheduled.
     */
    public function overviewSeries(): JsonResponse
    {
        $noCache = request()->boolean('noCache');
        $startParam = request('start');
        $endParam = request('end');
        $now = Carbon::now();
        $start = $startParam ? Carbon::parse($startParam)->startOfDay() : $now->copy()->startOfMonth();
        $end = $endParam ? Carbon::parse($endParam)->endOfDay() : $now->copy()->endOfDay();
        $days = $start->diffInDays($end) + 1;

        $cacheKey = 'dashboard_overview_series_'.$start->toDateString().'_'.$end->toDateString();

        $compute = function () use ($start, $end, $days) {

            $labels = [];
            $registrations = array_fill(0, $days, 0);
            $visits = array_fill(0, $days, 0);

            // Aggregate patients by day
            Patient::whereBetween('created_at', [$start, $end])
                ->get(['created_at'])
                ->each(function ($p) use (&$registrations, $start) {
                    $index = $p->created_at->diffInDays($start);
                    if (isset($registrations[$index])) {
                        $registrations[$index] += 1;
                    }
                });

            // Aggregate visits by day
            VisitService::whereBetween('scheduled_at', [$start, $end])
                ->get(['scheduled_at'])
                ->each(function ($v) use (&$visits, $start) {
                    $index = $v->scheduled_at->diffInDays($start);
                    if (isset($visits[$index])) {
                        $visits[$index] += 1;
                    }
                });

            for ($d = 1; $d <= $days; $d++) {
                $labels[] = $start->copy()->addDays($d - 1)->format('M d');
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
        };

        $data = $noCache ? $compute() : Cache::remember($cacheKey, 60, $compute);

        return response()->json($data);
    }

    /**
     * Return recent appointments (visit services) as JSON for the dashboard table.
     */
    public function recentAppointments(): JsonResponse
    {
        $startParam = request('start');
        $endParam = request('end');
        $start = $startParam ? Carbon::parse($startParam)->startOfDay() : null;
        $end = $endParam ? Carbon::parse($endParam)->endOfDay() : null;

        $query = VisitService::query()
            ->leftJoin('patients', 'visit_services.patient_id', '=', 'patients.id')
            ->leftJoin('staff', 'visit_services.staff_id', '=', 'staff.id')
            ->leftJoin('services', 'visit_services.service_id', '=', 'services.id')
            ->when($start && $end, fn ($q) => $q->whereBetween('visit_services.scheduled_at', [$start, $end]))
            ->orderByDesc('visit_services.scheduled_at')
            ->limit(8);

        // Log the query and its bindings for debugging
        \Illuminate\Support\Facades\Log::info('Recent Appointments Query:', ['sql' => $query->toSql(), 'bindings' => $query->getBindings()]);

        $rows = $query->get([
            'patients.full_name',
            'visit_services.scheduled_at',
            'visit_services.status',
            'staff.first_name as staff_first',
            'staff.last_name as staff_last',
            'services.name as service_name',
        ]);

        $payload = $rows->map(function ($row) {
            $name = $row->full_name ?: 'N/A';
            $dt = \Carbon\Carbon::parse($row->scheduled_at);

            return [
                'patient' => $name,
                'date' => $dt->format('Y-m-d'),
                'time' => $dt->format('h:i A'),
                'status' => $row->status,
                'staff' => trim(($row->staff_first ?? '').' '.($row->staff_last ?? '')) ?: null,
                'service' => $row->service_name ?? null,
            ];
        });

        return response()->json($payload);
    }

    /**
     * Upcoming visits within next 24 hours for empty-today fallback.
     */
    public function upcomingVisits(): JsonResponse
    {
        $now = Carbon::now();
        $next = $now->copy()->addDay()->endOfDay();

        $rows = VisitService::query()
            ->leftJoin('patients', 'visit_services.patient_id', '=', 'patients.id')
            ->leftJoin('staff', 'visit_services.staff_id', '=', 'staff.id')
            ->leftJoin('services', 'visit_services.service_id', '=', 'services.id')
            ->whereBetween('visit_services.scheduled_at', [$now, $next])
            ->orderBy('visit_services.scheduled_at')
            ->limit(8)
            ->get([
                'patients.full_name',
                'visit_services.scheduled_at',
                'visit_services.status',
                'staff.first_name as staff_first',
                'staff.last_name as staff_last',
                'services.name as service_name',
            ]);

        $payload = $rows->map(function ($row) {
            $name = $row->full_name ?: 'N/A';
            $dt = \Carbon\Carbon::parse($row->scheduled_at);

            return [
                'patient' => $name,
                'date' => $dt->format('Y-m-d'),
                'time' => $dt->format('h:i A'),
                'status' => $row->status,
                'staff' => trim(($row->staff_first ?? '').' '.($row->staff_last ?? '')) ?: null,
                'service' => $row->service_name ?? null,
            ];
        });

        return response()->json($payload);
    }

    /**
     * Reports: Service Volume time series within range (visits per day).
     */
    public function reportServiceVolume(): JsonResponse
    {
        $startParam = request('start');
        $endParam = request('end');
        $now = Carbon::now();
        $start = $startParam ? Carbon::parse($startParam)->startOfDay() : $now->copy()->startOfMonth();
        $end = $endParam ? Carbon::parse($endParam)->endOfDay() : $now->copy()->endOfDay();

        $days = $start->diffInDays($end) + 1;
        $labels = [];
        $series = array_fill(0, $days, 0);

        VisitService::whereBetween('scheduled_at', [$start, $end])
            ->get(['scheduled_at'])
            ->each(function ($v) use (&$series, $start) {
                $index = $v->scheduled_at->diffInDays($start);
                if (isset($series[$index])) {
                    $series[$index]++;
                }
            });

        for ($d = 1; $d <= $days; $d++) {
            $labels[] = $start->copy()->addDays($d - 1)->format('M d');
        }

        return response()->json([
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Visits',
                'backgroundColor' => '#3b82f6',
                'data' => $series,
            ]],
        ]);
    }

    /**
     * Reports: Revenue vs AR time series within range (sum by day).
     */
    public function reportRevenueAr(): JsonResponse
    {
        $startParam = request('start');
        $endParam = request('end');
        $now = Carbon::now();
        $start = $startParam ? Carbon::parse($startParam)->startOfDay() : $now->copy()->startOfMonth();
        $end = $endParam ? Carbon::parse($endParam)->endOfDay() : $now->copy()->endOfDay();

        $days = $start->diffInDays($end) + 1;
        $labels = [];
        $rev = array_fill(0, $days, 0.0);
        $ar = array_fill(0, $days, 0.0);

        // Revenue (paid invoices)
        Invoice::whereNotNull('paid_at')
            ->whereBetween('created_at', [$start, $end])
            ->get(['created_at', 'grand_total', 'amount'])
            ->each(function ($row) use (&$rev, $start) {
                $index = $row->created_at->diffInDays($start);
                $val = (float) ($row->grand_total ?: $row->amount ?: 0);
                if (isset($rev[$index])) {
                    $rev[$index] += $val;
                }
            });

        // AR (unpaid invoices)
        Invoice::whereNull('paid_at')
            ->whereBetween('created_at', [$start, $end])
            ->get(['created_at', 'grand_total', 'amount'])
            ->each(function ($row) use (&$ar, $start) {
                $index = $row->created_at->diffInDays($start);
                $val = (float) ($row->grand_total ?: $row->amount ?: 0);
                if (isset($ar[$index])) {
                    $ar[$index] += $val;
                }
            });

        for ($d = 1; $d <= $days; $d++) {
            $labels[] = $start->copy()->addDays($d - 1)->format('M d');
        }

        return response()->json([
            'labels' => $labels,
            'datasets' => [
                ['label' => 'Revenue', 'backgroundColor' => '#10b981', 'data' => $rev],
                ['label' => 'Accounts Receivable', 'backgroundColor' => '#f59e0b', 'data' => $ar],
            ],
        ]);
    }

    /**
     * Top services by volume and revenue within selected range.
     */
    public function topServices(): JsonResponse
    {
        $startParam = request('start');
        $endParam = request('end');
        $now = Carbon::now();
        $start = $startParam ? Carbon::parse($startParam)->startOfDay() : $now->copy()->startOfMonth();
        $end = $endParam ? Carbon::parse($endParam)->endOfDay() : $now->copy()->endOfDay();

        $rows = \DB::table('visit_services as v')
            ->join('services as s', 'v.service_id', '=', 's.id')
            ->whereBetween('v.scheduled_at', [$start, $end])
            ->select('s.id', 's.name', \DB::raw('COUNT(v.id) as volume'), \DB::raw('COALESCE(SUM(v.cost),0) as revenue'))
            ->groupBy('s.id', 's.name')
            ->orderByDesc('volume')
            ->limit(5)
            ->get();

        return response()->json($rows);
    }
}
