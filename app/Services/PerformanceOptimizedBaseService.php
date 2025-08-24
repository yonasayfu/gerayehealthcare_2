<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Exceptions\ResourceNotFoundException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PerformanceOptimizedBaseService extends BaseService
{
    protected $defaultWith = [];
    protected $cachePrefix = '';
    protected $cacheTtl = 300; // 5 minutes

    public function getAll(Request $request, array $with = [])
    {
        // Use default relationships if none specified
        $relationships = !empty($with) ? $with : $this->defaultWith;

        $query = $this->model->query()->with($relationships);

        if ($request->has('search')) {
            $this->applySearch($query, $request->input('search'));
        }

        if ($request->has('sort')) {
            $direction = $request->input('direction', 'asc');
            $query->orderBy($request->input('sort'), $direction);
        }

        // Always use pagination - never return all records
        return $query->paginate($request->input('per_page', 25));
    }

    public function getForSelect(string $displayField = 'name', string $valueField = 'id', int $limit = 1000)
    {
        $cacheKey = $this->cachePrefix . '_select_options';

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($displayField, $valueField, $limit) {
            return $this->model->select([$valueField, $displayField])
                ->orderBy($displayField)
                ->limit($limit)
                ->get();
        });
    }

    public function getById(int $id, array $with = [])
    {
        $relationships = !empty($with) ? $with : $this->defaultWith;
        $cacheKey = $this->cachePrefix . '_single_' . $id;

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($id, $relationships) {
            $query = $this->model->query()->with($relationships);
            $model = $query->find($id);
            if (!$model) {
                throw new ResourceNotFoundException(class_basename($this->model) . ' not found.');
            }
            return $model;
        });
    }

    public function create(array|object $data)
    {
        $data = is_object($data) ? (array) $data : $data;

        $model = $this->model->create($data);

        // Clear related caches
        $this->clearCache();

        return $model;
    }

    public function update(int $id, array|object $data)
    {
        $data = is_object($data) ? (array) $data : $data;
        $model = $this->model->findOrFail($id);
        $model->update($data);

        // Clear related caches
        $this->clearCache();

        return $model;
    }

    public function delete(int $id): void
    {
        $this->model->findOrFail($id)->delete();

        // Clear related caches
        $this->clearCache();
    }

    /**
     * Get dashboard statistics with caching
     */
    public function getDashboardStats(): array
    {
        $cacheKey = $this->cachePrefix . '_dashboard_stats';

        return Cache::remember($cacheKey, 600, function () { // 10 minutes for stats
            return [
                'total' => $this->model->count(),
                'active' => $this->model->where('status', 'Active')->count(),
                'recent' => $this->model->whereDate('created_at', '>=', now()->subDays(7))->count(),
            ];
        });
    }

    /**
     * Clear all related caches
     */
    protected function clearCache(): void
    {
        $pattern = $this->cachePrefix . '_*';
        // In production, you might want to use Redis for pattern-based deletion
        Cache::flush(); // For now, simple approach
    }

    protected function applySearch($query, $search)
    {
        // This method should be overridden in the child service class
    }
}
