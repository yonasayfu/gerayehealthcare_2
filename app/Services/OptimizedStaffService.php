<?php

namespace App\Services;

use App\Http\Config\ExportConfig;
use App\Http\Traits\ExportableTrait;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OptimizedStaffService extends OptimizedBaseService
{
    use ExportableTrait;

    protected $cacheTtl = 900; // 15 minutes for staff data (changes less frequently)

    public function __construct(Staff $staff)
    {
        parent::__construct($staff);
        $this->cachePrefix = 'staff';
    }

    public function getAll(Request $request, array $with = [])
    {
        // Always include user relationship to prevent N+1 queries
        $defaultWith = ['user'];
        $with = array_merge($defaultWith, $with);

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
                $query->orderBy('first_name')->orderBy('last_name');
            }

            return $query->paginate($request->input('per_page', 15));
        });
    }

    public function getById(int $id, array $with = [])
    {
        // Always include user relationship for staff details
        $defaultWith = ['user'];
        $with = array_merge($defaultWith, $with);

        $cacheKey = $this->generateCacheKey('single', ['id' => $id], $with);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($id, $with) {
            $staff = $this->model->query()->with($with)->find($id);

            if (! $staff) {
                throw new \App\Exceptions\ResourceNotFoundException('Staff member not found.');
            }

            return $staff;
        });
    }

    public function create(array|object $data): Staff
    {
        $data = is_object($data) ? (array) $data : $data;

        DB::beginTransaction();
        try {
            // Optimize file handling
            $data = $this->handleFileUpload($data);

            // Normalize hourly_rate if provided
            if (array_key_exists('hourly_rate', $data)) {
                if ($data['hourly_rate'] === '' || $data['hourly_rate'] === null) {
                    $data['hourly_rate'] = null;
                } elseif (is_string($data['hourly_rate']) || is_numeric($data['hourly_rate'])) {
                    $data['hourly_rate'] = (float) $data['hourly_rate'];
                }
            }

            // Avoid sending nulls that can overwrite NOT NULL columns unintentionally
            $data = array_filter($data, fn ($v) => ! is_null($v));

            $staff = $this->model->create($data);

            // Clear related caches
            $this->clearCaches();

            DB::commit();

            return $staff;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(int $id, array|object $data): Staff
    {
        $data = is_object($data) ? (array) $data : $data;

        DB::beginTransaction();
        try {
            $staff = $this->getById($id);

            // Normalize hourly_rate if present in payload
            if (array_key_exists('hourly_rate', $data)) {
                if ($data['hourly_rate'] === '' || $data['hourly_rate'] === null) {
                    unset($data['hourly_rate']);
                } elseif (is_string($data['hourly_rate']) || is_numeric($data['hourly_rate'])) {
                    $data['hourly_rate'] = (float) $data['hourly_rate'];
                }
            }

            // Handle file upload and deletion efficiently
            if (isset($data['photo'])) {
                // Delete old photo if exists
                if ($staff->photo && Storage::disk('public')->exists($staff->photo)) {
                    Storage::disk('public')->delete($staff->photo);
                }
                $data['photo'] = $data['photo']->store('images/staff', 'public');
            } else {
                unset($data['photo']);
            }

            // Avoid sending nulls that can overwrite NOT NULL columns
            $data = array_filter($data, fn ($v) => $v !== null);

            $staff->update($data);

            // Clear related caches
            $this->clearCaches();

            DB::commit();

            return $staff;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function applySearch($query, $search)
    {
        // Optimized search with full-text search capabilities
        $query->where(function ($q) use ($search) {
            $q->where('first_name', 'ilike', "%{$search}%")
                ->orWhere('last_name', 'ilike', "%{$search}%")
                ->orWhere('email', 'ilike', "%{$search}%")
                ->orWhere('phone', 'ilike', "%{$search}%")
                ->orWhere('position', 'ilike', "%{$search}%")
                ->orWhere('department', 'ilike', "%{$search}%");
        });
    }

    // Cache form data for create/edit forms
    public function getFormData()
    {
        return Cache::remember('staff_form_data', 3600, function () { // 1 hour
            return [
                'departments' => config('hr.departments'),
                'positions' => config('hr.positions'),
            ];
        });
    }

    // Get staff statistics for dashboard (cached)
    public function getStatistics()
    {
        return Cache::remember('staff_statistics', 3600, function () { // 1 hour
            return [
                'total_staff' => $this->model->count(),
                'active_staff' => $this->model->where('status', 'active')->count(),
                'new_this_month' => $this->model->whereMonth('hire_date', now()->month)->count(),
                'by_department' => $this->model->select('department', DB::raw('count(*) as count'))
                    ->groupBy('department')
                    ->pluck('count', 'department')
                    ->toArray(),
            ];
        });
    }

    // Get available staff for assignments (cached)
    public function getAvailableStaff(?string $date = null, ?string $timeSlot = null)
    {
        $cacheKey = 'available_staff_'.md5(($date ?? 'today').'_'.($timeSlot ?? 'all'));

        return Cache::remember($cacheKey, 600, function () use ($date, $timeSlot) { // 10 minutes
            $query = $this->model->where('status', 'active');

            // Add availability logic here if needed
            if ($date && $timeSlot) {
                // This would check against staff availability tables
                // Implementation depends on your availability system
            }

            return $query->select('id', 'first_name', 'last_name', 'position')
                ->orderBy('first_name')
                ->get();
        });
    }

    private function handleFileUpload(array $data): array
    {
        if (isset($data['photo']) && $data['photo']) {
            // Optimize file storage with better naming
            $filename = 'staff_'.time().'_'.uniqid().'.'.$data['photo']->getClientOriginalExtension();
            $data['photo'] = $data['photo']->storeAs('images/staff', $filename, 'public');
        } else {
            unset($data['photo']);
        }

        return $data;
    }

    // Optimized export methods
    public function export(Request $request)
    {
        return $this->handleExport($request, Staff::class, ExportConfig::getStaffConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, Staff::class, ExportConfig::getStaffConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, Staff::class, ExportConfig::getStaffConfig());
    }

    public function printSingle($id, Request $request)
    {
        $staff = $this->getById($id);

        return $this->handlePrintSingle($request, $staff, ExportConfig::getStaffConfig());
    }

    protected function clearCaches(): void
    {
        // Clear specific staff caches
        Cache::forget('staff_form_data');
        Cache::forget('staff_statistics');

        // Clear availability caches (pattern matching would be better with Redis)
        $patterns = ['staff_all_*', 'staff_single_*', 'available_staff_*'];
        foreach ($patterns as $pattern) {
            Cache::flush(); // Simplified for database cache
            break;
        }
    }
}
