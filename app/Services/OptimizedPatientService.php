<?php

namespace App\Services;

use App\DTOs\CreatePatientDTO;
use App\Models\Patient;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\ExportConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OptimizedPatientService extends OptimizedBaseService
{
    use ExportableTrait;

    protected $cacheTtl = 600; // 10 minutes for patient data

    public function __construct(Patient $patient)
    {
        parent::__construct($patient);
        $this->cachePrefix = 'patients';
    }

    public function getAll(Request $request, array $with = [])
    {
        // Always include these relationships to prevent N+1 queries
        $defaultWith = ['corporateClient', 'insurancePolicy'];
        $with = array_merge($defaultWith, $with);

        // Create more specific cache key including relationships
        $cacheKey = $this->generateCacheKey('all', [
            'search' => $request->input('search'),
            'sort' => $request->input('sort'),
            'direction' => $request->input('direction'),
            'per_page' => $request->input('per_page', 15),
        ], $with);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($request, $with) {
            $query = $this->model->query()->with($with);

            if ($request->has('search')) {
                $this->applySearch($query, $request->input('search'));
            }

            if ($request->has('sort')) {
                $direction = $request->input('direction', 'asc');
                $query->orderBy($request->input('sort'), $direction);
            } else {
                // Default ordering for better performance
                $query->latest('created_at');
            }

            return $query->paginate($request->input('per_page', 15));
        });
    }

    public function getById(int $id, array $with = [])
    {
        // Always include these relationships for show pages
        $defaultWith = ['corporateClient', 'insurancePolicy', 'visitServices'];
        $with = array_merge($defaultWith, $with);

        $cacheKey = $this->generateCacheKey('single', ['id' => $id], $with);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($id, $with) {
            $patient = $this->model->query()->with($with)->find($id);
            
            if (!$patient) {
                throw new \App\Exceptions\ResourceNotFoundException('Patient not found.');
            }
            
            return $patient;
        });
    }

    public function create(array|object $data)
    {
        $data = is_object($data) ? (array) $data : $data;
        
        DB::beginTransaction();
        try {
            $patient = $this->model->create($data);
            
            // Clear related caches
            $this->clearCaches();
            
            DB::commit();
            return $patient;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(int $id, array|object $data)
    {
        $data = is_object($data) ? (array) $data : $data;
        
        DB::beginTransaction();
        try {
            $patient = $this->model->findOrFail($id);
            $patient->update($data);
            
            // Clear related caches
            $this->clearCaches();
            
            DB::commit();
            return $patient;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function applySearch($query, $search)
    {
        // Use database full-text search for better performance
        $query->where(function ($q) use ($search) {
            $q->where('full_name', 'ilike', "%{$search}%")
              ->orWhere('patient_code', 'ilike', "%{$search}%")
              ->orWhere('fayda_id', 'ilike', "%{$search}%")
              ->orWhere('phone_number', 'ilike', "%{$search}%");
        });
    }

    // Optimized export with chunking for large datasets
    public function export(Request $request)
    {
        return $this->handleExport($request, Patient::class, ExportConfig::getPatientConfig());
    }

    public function printSingle($id, Request $request)
    {
        $patient = $this->getById($id, ['corporateClient', 'insurancePolicy']);
        $config = ExportConfig::getPatientConfig();
        
        return $this->handlePrintSingle($request, $patient, $config);
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, Patient::class, ExportConfig::getPatientConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, Patient::class, ExportConfig::getPatientConfig());
    }

    // Cache form data for create/edit forms
    public function getFormData()
    {
        return Cache::remember('patient_form_data', 1800, function () { // 30 minutes
            return [
                'corporateClients' => \App\Models\CorporateClient::select('id', 'name')->orderBy('name')->get(),
                'insurancePolicies' => \App\Models\InsurancePolicy::with('corporateClient:id,name')
                    ->select('id', 'policy_number', 'corporate_client_id')
                    ->orderBy('policy_number')
                    ->get(),
            ];
        });
    }

    // Get patient statistics for dashboard (cached)
    public function getStatistics()
    {
        return Cache::remember('patient_statistics', 3600, function () { // 1 hour
            return [
                'total_patients' => $this->model->count(),
                'new_this_month' => $this->model->whereMonth('created_at', now()->month)->count(),
                'active_patients' => $this->model->whereHas('visitServices', function ($q) {
                    $q->where('created_at', '>=', now()->subMonth());
                })->count(),
            ];
        });
    }

    protected function clearCaches(): void
    {
        // Clear specific patient caches
        Cache::forget('patient_form_data');
        Cache::forget('patient_statistics');
        
        // Clear paginated results (this is simplified - in production you'd want more targeted clearing)
        $patterns = ['patients_all_*', 'patients_single_*'];
        foreach ($patterns as $pattern) {
            // Note: This is a simplified approach. In production with Redis, you'd use tags or patterns
            Cache::flush();
            break; // Just flush all for now since we're using database cache
        }
    }
}