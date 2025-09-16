<?php

namespace App\Services\Optimized;

use App\Services\Optimized\PerformanceOptimizedBaseService;

use App\Http\Config\ExportConfig;
use App\Http\Traits\ExportableTrait;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PatientService extends BaseService
{
    use ExportableTrait;

    protected $defaultWith = ['visitServices', 'invoices'];

    protected $cachePrefix = 'patients';

    public function __construct(Patient $patient)
    {
        parent::__construct($patient);
    }

    protected function applySearch($query, $search)
    {
        $query->where('full_name', 'ilike', "%{$search}%")
            ->orWhere('phone_number', 'ilike', "%{$search}%")
            ->orWhere('email', 'ilike', "%{$search}%")
            ->orWhere('patient_code', 'ilike', "%{$search}%");
    }

    /**
     * Get patients for select dropdowns (optimized)
     */
    public function getPatientsForSelect()
    {
        return $this->getForSelect('full_name', 'id');
    }

    /**
     * Get recent patients with proper relationships
     */
    public function getRecentPatients(int $limit = 10)
    {
        return Cache::remember('patients_recent_'.$limit, 300, function () use ($limit) {
            return $this->model->with(['visitServices' => function ($query) {
                $query->latest()->limit(3);
            }, 'invoices' => function ($query) {
                $query->latest()->limit(3);
            }])
                ->latest()
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get patient dashboard statistics
     */
    public function getDashboardStats(): array
    {
        return Cache::remember('patients_dashboard_stats', 600, function () {
            return [
                'total' => $this->model->count(),
                'new_this_week' => $this->model->whereDate('created_at', '>=', now()->subDays(7))->count(),
                'with_appointments' => $this->model->whereHas('visitServices', function ($query) {
                    $query->where('scheduled_at', '>=', now());
                })->count(),
                'by_source' => $this->model->select('source')
                    ->selectRaw('count(*) as count')
                    ->groupBy('source')
                    ->get()
                    ->pluck('count', 'source')
                    ->toArray(),
            ];
        });
    }

    /**
     * Export functionality
     */
    public function export(Request $request)
    {
        return $this->handleExport($request, Patient::class, ExportConfig::getPatientConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, Patient::class, ExportConfig::getPatientConfig());
    }

    public function printSingle(Request $request, Patient $patient)
    {
        return $this->handlePrintSingle($request, $patient, ExportConfig::getPatientConfig());
    }
}
