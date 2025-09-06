<?php

namespace App\Services;

use App\Exceptions\ResourceNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class OptimizedBaseService
{
    protected $model;

    protected $cachePrefix;

    protected $cacheTtl = 300; // 5 minutes

    public function __construct($model)
    {
        $this->model = $model;
        $this->cachePrefix = class_basename($model);
    }

    public function getAll(Request $request, array $with = [])
    {
        // Create cache key based on request parameters
        $cacheKey = $this->generateCacheKey('all', $request->all(), $with);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($request, $with) {
            $query = $this->model->query()->with($with);

            if ($request->has('search')) {
                $this->applySearch($query, $request->input('search'));
            }

            if ($request->has('sort')) {
                $direction = $request->input('direction', 'asc');
                $query->orderBy($request->input('sort'), $direction);
            }

            return $query->paginate($request->input('per_page', 15));
        });
    }

    public function getById(int $id, array $with = [])
    {
        $cacheKey = $this->generateCacheKey('single', ['id' => $id], $with);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($id, $with) {
            $query = $this->model->query()->with($with);
            $model = $query->find($id);
            if (! $model) {
                throw new ResourceNotFoundException(class_basename($this->model).' not found.');
            }

            return $model;
        });
    }

    public function create(array|object $data)
    {
        $data = is_object($data) ? (array) $data : $data;

        $model = $this->model->create($data);

        // Clear related caches
        $this->clearCaches();

        return $model;
    }

    public function update(int $id, array|object $data)
    {
        $data = is_object($data) ? (array) $data : $data;
        $model = $this->model->findOrFail($id);
        $model->update($data);

        // Clear related caches
        $this->clearCaches();

        return $model;
    }

    public function delete(int $id): void
    {
        $this->model->findOrFail($id)->delete();

        // Clear related caches
        $this->clearCaches();
    }

    protected function applySearch($query, $search)
    {
        // This method should be overridden in the child service class
    }

    protected function generateCacheKey(string $operation, array $params = [], array $with = []): string
    {
        $keyData = [
            'prefix' => $this->cachePrefix,
            'operation' => $operation,
            'params' => $params,
            'with' => $with,
        ];

        return 'service_'.md5(serialize($keyData));
    }

    protected function clearCaches(): void
    {
        // Clear all caches for this model
        Cache::tags([$this->cachePrefix])->flush();
    }
}
