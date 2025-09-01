<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Models\Patient;
use App\Models\VisitService;
use App\Models\Staff;
use App\Models\MarketingCampaign;
use App\Models\Lead;
use App\Models\InsuranceClaim;
use App\Models\InventoryItem;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends BaseApiController
{
    /**
     * Get dashboard analytics
     */
    public function dashboard(Request $request): JsonResponse
    {
        try {
            $period = $request->get('period', '30'); // days
            $startDate = now()->subDays($period);

            $analytics = [
                'overview' => $this->getOverviewStats($startDate),
                'patients' => $this->getPatientStats($startDate),
                'visits' => $this->getVisitStats($startDate),
                'revenue' => $this->getRevenueStats($startDate),
                'staff' => $this->getStaffStats($startDate),
                'marketing' => $this->getMarketingStats($startDate),
                'recent_activities' => $this->getRecentActivities(),
            ];

            return $this->successResponse($analytics);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch dashboard analytics', 500);
        }
    }

    /**
     * Get patient analytics
     */
    public function patients(Request $request): JsonResponse
    {
        try {
            $startDate = $request->get('start_date', now()->subDays(30)->toDateString());
            $endDate = $request->get('end_date', now()->toDateString());

            $analytics = [
                'summary' => [
                    'total_patients' => Patient::count(),
                    'new_patients' => Patient::whereBetween('created_at', [$startDate, $endDate])->count(),
                    'active_patients' => Patient::where('status', 'active')->count(),
                    'patients_with_visits' => Patient::whereHas('visitServices', function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('visit_date', [$startDate, $endDate]);
                    })->count(),
                ],
                'demographics' => [
                    'by_age_group' => $this->getPatientsByAgeGroup(),
                    'by_gender' => Patient::selectRaw('gender, COUNT(*) as count')
                        ->groupBy('gender')
                        ->get(),
                    'by_location' => Patient::selectRaw('city, COUNT(*) as count')
                        ->whereNotNull('city')
                        ->groupBy('city')
                        ->orderBy('count', 'desc')
                        ->limit(10)
                        ->get(),
                ],
                'acquisition' => [
                    'by_source' => Patient::selectRaw('lead_source, COUNT(*) as count')
                        ->whereNotNull('lead_source')
                        ->groupBy('lead_source')
                        ->orderBy('count', 'desc')
                        ->get(),
                    'by_campaign' => Patient::join('marketing_campaigns', 'patients.marketing_campaign_id', '=', 'marketing_campaigns.id')
                        ->selectRaw('marketing_campaigns.name, COUNT(*) as count')
                        ->groupBy('marketing_campaigns.id', 'marketing_campaigns.name')
                        ->orderBy('count', 'desc')
                        ->get(),
                ],
                'trends' => $this->getPatientTrends($startDate, $endDate),
            ];

            return $this->successResponse($analytics);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch patient analytics', 500);
        }
    }

    /**
     * Get visit analytics
     */
    public function visits(Request $request): JsonResponse
    {
        try {
            $startDate = $request->get('start_date', now()->subDays(30)->toDateString());
            $endDate = $request->get('end_date', now()->toDateString());

            $analytics = [
                'summary' => [
                    'total_visits' => VisitService::whereBetween('visit_date', [$startDate, $endDate])->count(),
                    'completed_visits' => VisitService::where('status', 'completed')
                        ->whereBetween('visit_date', [$startDate, $endDate])->count(),
                    'cancelled_visits' => VisitService::where('status', 'cancelled')
                        ->whereBetween('visit_date', [$startDate, $endDate])->count(),
                    'average_visit_duration' => VisitService::whereBetween('visit_date', [$startDate, $endDate])
                        ->whereNotNull('actual_duration')
                        ->avg('actual_duration'),
                ],
                'by_service_type' => VisitService::selectRaw('service_type, COUNT(*) as count, AVG(total_cost) as avg_cost')
                    ->whereBetween('visit_date', [$startDate, $endDate])
                    ->groupBy('service_type')
                    ->orderBy('count', 'desc')
                    ->get(),
                'by_staff' => VisitService::join('staff', 'visit_services.assigned_staff_id', '=', 'staff.id')
                    ->join('users', 'staff.user_id', '=', 'users.id')
                    ->selectRaw('users.name, COUNT(*) as visit_count, AVG(visit_services.total_cost) as avg_revenue')
                    ->whereBetween('visit_services.visit_date', [$startDate, $endDate])
                    ->groupBy('users.id', 'users.name')
                    ->orderBy('visit_count', 'desc')
                    ->get(),
                'performance_metrics' => [
                    'on_time_percentage' => $this->getOnTimePercentage($startDate, $endDate),
                    'completion_rate' => $this->getCompletionRate($startDate, $endDate),
                    'customer_satisfaction' => $this->getAverageRating($startDate, $endDate),
                ],
                'trends' => $this->getVisitTrends($startDate, $endDate),
            ];

            return $this->successResponse($analytics);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch visit analytics', 500);
        }
    }

    /**
     * Get revenue analytics
     */
    public function revenue(Request $request): JsonResponse
    {
        try {
            $startDate = $request->get('start_date', now()->subDays(30)->toDateString());
            $endDate = $request->get('end_date', now()->toDateString());

            $analytics = [
                'summary' => [
                    'total_revenue' => VisitService::whereBetween('visit_date', [$startDate, $endDate])
                        ->sum('total_cost'),
                    'average_visit_value' => VisitService::whereBetween('visit_date', [$startDate, $endDate])
                        ->avg('total_cost'),
                    'paid_amount' => VisitService::whereBetween('visit_date', [$startDate, $endDate])
                        ->sum('paid_amount'),
                    'outstanding_amount' => VisitService::whereBetween('visit_date', [$startDate, $endDate])
                        ->whereRaw('total_cost > paid_amount')
                        ->selectRaw('SUM(total_cost - paid_amount)')
                        ->value('sum') ?? 0,
                ],
                'by_service_type' => VisitService::selectRaw('service_type, SUM(total_cost) as revenue, COUNT(*) as visits')
                    ->whereBetween('visit_date', [$startDate, $endDate])
                    ->groupBy('service_type')
                    ->orderBy('revenue', 'desc')
                    ->get(),
                'by_payment_method' => VisitService::selectRaw('payment_method, SUM(paid_amount) as amount, COUNT(*) as count')
                    ->whereBetween('visit_date', [$startDate, $endDate])
                    ->whereNotNull('payment_method')
                    ->groupBy('payment_method')
                    ->get(),
                'monthly_trends' => $this->getRevenueTrends(),
                'top_patients' => $this->getTopPatientsByRevenue($startDate, $endDate),
            ];

            return $this->successResponse($analytics);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch revenue analytics', 500);
        }
    }

    /**
     * Get staff performance analytics
     */
    public function staff(Request $request): JsonResponse
    {
        try {
            $startDate = $request->get('start_date', now()->subDays(30)->toDateString());
            $endDate = $request->get('end_date', now()->toDateString());

            $analytics = [
                'summary' => [
                    'total_staff' => Staff::where('status', 'active')->count(),
                    'available_staff' => Staff::where('status', 'active')
                        ->where('availability_status', 'available')->count(),
                    'busy_staff' => Staff::where('status', 'active')
                        ->where('availability_status', 'busy')->count(),
                ],
                'performance' => Staff::join('users', 'staff.user_id', '=', 'users.id')
                    ->leftJoin('visit_services', 'staff.id', '=', 'visit_services.assigned_staff_id')
                    ->selectRaw('
                        users.name,
                        staff.specialization,
                        COUNT(visit_services.id) as total_visits,
                        AVG(visit_services.rating) as avg_rating,
                        SUM(visit_services.total_cost) as total_revenue
                    ')
                    ->whereBetween('visit_services.visit_date', [$startDate, $endDate])
                    ->groupBy('users.id', 'users.name', 'staff.specialization')
                    ->orderBy('total_visits', 'desc')
                    ->get(),
                'workload' => $this->getStaffWorkload($startDate, $endDate),
                'ratings' => $this->getStaffRatings($startDate, $endDate),
            ];

            return $this->successResponse($analytics);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch staff analytics', 500);
        }
    }

    // Helper methods
    private function getOverviewStats($startDate): array
    {
        return [
            'total_patients' => Patient::count(),
            'new_patients_today' => Patient::whereDate('created_at', today())->count(),
            'visits_today' => VisitService::whereDate('visit_date', today())->count(),
            'revenue_today' => VisitService::whereDate('visit_date', today())->sum('total_cost'),
            'active_staff' => Staff::where('status', 'active')->count(),
            'pending_claims' => InsuranceClaim::whereIn('status', ['submitted', 'under_review'])->count(),
            'low_stock_items' => InventoryItem::whereRaw('current_stock <= reorder_level')->count(),
            'unread_messages' => Message::whereNull('read_at')->count(),
        ];
    }

    private function getPatientStats($startDate): array
    {
        return [
            'new_registrations' => Patient::where('created_at', '>=', $startDate)->count(),
            'active_patients' => Patient::where('status', 'active')->count(),
            'patients_with_recent_visits' => Patient::whereHas('visitServices', function ($query) use ($startDate) {
                $query->where('visit_date', '>=', $startDate);
            })->count(),
        ];
    }

    private function getVisitStats($startDate): array
    {
        return [
            'total_visits' => VisitService::where('visit_date', '>=', $startDate)->count(),
            'completed_visits' => VisitService::where('status', 'completed')
                ->where('visit_date', '>=', $startDate)->count(),
            'upcoming_visits' => VisitService::where('status', 'scheduled')
                ->where('visit_date', '>=', now())->count(),
        ];
    }

    private function getRevenueStats($startDate): array
    {
        return [
            'total_revenue' => VisitService::where('visit_date', '>=', $startDate)->sum('total_cost'),
            'paid_amount' => VisitService::where('visit_date', '>=', $startDate)->sum('paid_amount'),
            'average_visit_value' => VisitService::where('visit_date', '>=', $startDate)->avg('total_cost'),
        ];
    }

    private function getStaffStats($startDate): array
    {
        return [
            'total_staff' => Staff::where('status', 'active')->count(),
            'available_staff' => Staff::where('availability_status', 'available')->count(),
            'staff_with_visits' => Staff::whereHas('visitServices', function ($query) use ($startDate) {
                $query->where('visit_date', '>=', $startDate);
            })->count(),
        ];
    }

    private function getMarketingStats($startDate): array
    {
        return [
            'active_campaigns' => MarketingCampaign::where('status', 'active')->count(),
            'new_leads' => Lead::where('created_at', '>=', $startDate)->count(),
            'converted_leads' => Lead::where('converted_to_patient', true)
                ->where('created_at', '>=', $startDate)->count(),
        ];
    }

    private function getRecentActivities(): array
    {
        return [
            'recent_patients' => Patient::with('staff')->latest()->limit(5)->get(),
            'recent_visits' => VisitService::with(['patient', 'assignedStaff.user'])->latest()->limit(5)->get(),
            'recent_messages' => Message::with(['sender', 'receiver'])->latest()->limit(5)->get(),
        ];
    }

    private function getPatientsByAgeGroup(): array
    {
        return Patient::selectRaw('
            CASE 
                WHEN EXTRACT(YEAR FROM AGE(date_of_birth)) < 18 THEN \'Under 18\'
                WHEN EXTRACT(YEAR FROM AGE(date_of_birth)) BETWEEN 18 AND 30 THEN \'18-30\'
                WHEN EXTRACT(YEAR FROM AGE(date_of_birth)) BETWEEN 31 AND 50 THEN \'31-50\'
                WHEN EXTRACT(YEAR FROM AGE(date_of_birth)) BETWEEN 51 AND 70 THEN \'51-70\'
                ELSE \'Over 70\'
            END as age_group,
            COUNT(*) as count
        ')
        ->whereNotNull('date_of_birth')
        ->groupBy('age_group')
        ->get()
        ->toArray();
    }

    private function getPatientTrends($startDate, $endDate): array
    {
        return Patient::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    private function getVisitTrends($startDate, $endDate): array
    {
        return VisitService::selectRaw('DATE(visit_date) as date, COUNT(*) as count, SUM(total_cost) as revenue')
            ->whereBetween('visit_date', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    private function getRevenueTrends(): array
    {
        return VisitService::selectRaw('DATE_TRUNC(\'month\', visit_date) as month, SUM(total_cost) as revenue')
            ->where('visit_date', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->toArray();
    }

    private function getTopPatientsByRevenue($startDate, $endDate): array
    {
        return Patient::join('visit_services', 'patients.id', '=', 'visit_services.patient_id')
            ->selectRaw('patients.full_name, SUM(visit_services.total_cost) as total_spent, COUNT(visit_services.id) as visit_count')
            ->whereBetween('visit_services.visit_date', [$startDate, $endDate])
            ->groupBy('patients.id', 'patients.full_name')
            ->orderBy('total_spent', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
    }

    private function getOnTimePercentage($startDate, $endDate): float
    {
        $totalVisits = VisitService::whereBetween('visit_date', [$startDate, $endDate])->count();
        $onTimeVisits = VisitService::whereBetween('visit_date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->whereRaw('actual_start_time <= scheduled_start_time + INTERVAL \'15 minutes\'')
            ->count();

        return $totalVisits > 0 ? round(($onTimeVisits / $totalVisits) * 100, 2) : 0;
    }

    private function getCompletionRate($startDate, $endDate): float
    {
        $totalVisits = VisitService::whereBetween('visit_date', [$startDate, $endDate])->count();
        $completedVisits = VisitService::where('status', 'completed')
            ->whereBetween('visit_date', [$startDate, $endDate])->count();

        return $totalVisits > 0 ? round(($completedVisits / $totalVisits) * 100, 2) : 0;
    }

    private function getAverageRating($startDate, $endDate): float
    {
        return VisitService::whereBetween('visit_date', [$startDate, $endDate])
            ->whereNotNull('rating')
            ->avg('rating') ?? 0;
    }

    private function getStaffWorkload($startDate, $endDate): array
    {
        return Staff::join('users', 'staff.user_id', '=', 'users.id')
            ->leftJoin('visit_services', 'staff.id', '=', 'visit_services.assigned_staff_id')
            ->selectRaw('
                users.name,
                COUNT(visit_services.id) as total_visits,
                SUM(CASE WHEN visit_services.status = \'completed\' THEN 1 ELSE 0 END) as completed_visits,
                AVG(visit_services.actual_duration) as avg_duration
            ')
            ->whereBetween('visit_services.visit_date', [$startDate, $endDate])
            ->groupBy('users.id', 'users.name')
            ->get()
            ->toArray();
    }

    private function getStaffRatings($startDate, $endDate): array
    {
        return Staff::join('users', 'staff.user_id', '=', 'users.id')
            ->join('visit_services', 'staff.id', '=', 'visit_services.assigned_staff_id')
            ->selectRaw('
                users.name,
                AVG(visit_services.rating) as avg_rating,
                COUNT(visit_services.rating) as rating_count
            ')
            ->whereBetween('visit_services.visit_date', [$startDate, $endDate])
            ->whereNotNull('visit_services.rating')
            ->groupBy('users.id', 'users.name')
            ->orderBy('avg_rating', 'desc')
            ->get()
            ->toArray();
    }
}
