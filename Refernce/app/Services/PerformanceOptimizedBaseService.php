<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

abstract class PerformanceOptimizedBaseService extends BaseService
{
    /**
     * Cache prefix for this service
     */
    protected string $cachePrefix = 'service';

    /**
     * Cache TTL in seconds
     */
    protected int $cacheTtl = 300; // 5 minutes

    /**
     * Enable or disable caching for this service
     */
    protected bool $enableCaching = true;

    /**
     * Tags for cache invalidation
     */
    protected array $cacheTags = [];

    /**
     * Get all records with caching
     */
    public function getAll(Request $request, array $with = []): LengthAwarePaginator
    {
        if (! $this->enableCaching) {
            return parent::getAll($request, $with);
        }

        $cacheKey = $this->generateCacheKey('all', $request->all(), $with);

        return $this->remember($cacheKey, function () use ($request, $with) {
            return parent::getAll($request, $with);
        });
    }

    /**
     * Find a record by ID with caching
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function findById(int $id, array $with = [])
    {
        if (! $this->enableCaching) {
            return parent::findById($id, $with);
        }

        $cacheKey = $this->generateCacheKey('find', ['id' => $id], $with);

        return $this->remember($cacheKey, function () use ($id, $with) {
            return parent::findById($id, $with);
        });
    }

    /**
     * Create a new record and invalidate cache
     */
    public function create(array $data): Model
    {
        $result = parent::create($data);
        $this->invalidateCache();

        return $result;
    }

    /**
     * Update an existing record and invalidate cache
     */
    public function update(int $id, array $data): Model
    {
        $result = parent::update($id, $data);
        $this->invalidateCache();

        return $result;
    }

    /**
     * Delete a record and invalidate cache
     */
    public function delete(int $id): ?bool
    {
        $result = parent::delete($id);
        $this->invalidateCache();

        return $result;
    }

    /**
     * Bulk delete records and invalidate cache
     */
    public function bulkDelete(array $ids): int
    {
        $result = parent::bulkDelete($ids);
        $this->invalidateCache();

        return $result;
    }

    /**
     * Get cached data or execute callback and cache result
     *
     * @return mixed
     */
    protected function remember(string $key, callable $callback, ?int $ttl = null)
    {
        $ttl = $ttl ?? $this->cacheTtl;

        if (! empty($this->cacheTags) && Cache::supportsTags()) {
            return Cache::tags($this->cacheTags)->remember($key, $ttl, $callback);
        }

        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Forget cached data by key
     */
    protected function forgetCache(string $key): bool
    {
        if (! empty($this->cacheTags) && Cache::supportsTags()) {
            return Cache::tags($this->cacheTags)->forget($key);
        }

        return Cache::forget($key);
    }

    /**
     * Invalidate all cache for this service
     */
    protected function invalidateCache(): void
    {
        if (! empty($this->cacheTags) && Cache::supportsTags()) {
            Cache::tags($this->cacheTags)->flush();
        }
    }

    /**
     * Generate cache key based on parameters
     */
    protected function generateCacheKey(string $operation, array $params = [], array $with = []): string
    {
        $keyParts = [
            $this->cachePrefix,
            $operation,
            md5(serialize($params)),
            md5(serialize($with)),
        ];

        return implode(':', array_filter($keyParts));
    }

    /**
     * Optimize database query with indexing hints
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function optimizeQuery($query)
    {
        // Add any query optimizations here
        // For example, use specific indexes, force index usage, etc.
        return $query;
    }

    /**
     * Apply performance optimizations to search
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     */
    protected function applySearch($query, string $search): void
    {
        // Call parent implementation
        parent::applySearch($query, $search);

        // Add performance optimizations
        // For example, use full-text search indexes if available
    }

    /**
     * Run a database transaction with performance considerations
     *
     * @return mixed
     */
    protected function transaction(callable $callback)
    {
        // Disable query log in production for performance
        if (app()->environment('production')) {
            DB::disableQueryLog();
        }

        try {
            return DB::transaction($callback);
        } finally {
            // Re-enable query log if it was previously enabled
            if (app()->environment('local') || app()->environment('development')) {
                DB::enableQueryLog();
            }
        }
    }

    /**
     * Batch process records for better performance
     */
    protected function batchProcess(array $items, callable $processor, int $batchSize = 100): array
    {
        $results = [];

        foreach (array_chunk($items, $batchSize) as $batch) {
            $batchResults = [];
            foreach ($batch as $item) {
                $batchResults[] = $processor($item);
            }
            $results = array_merge($results, $batchResults);

            // Clear memory after each batch
            if (function_exists('gc_collect_cycles')) {
                gc_collect_cycles();
            }
        }

        return $results;
    }

    /**
     * Set cache TTL for this service
     */
    public function setCacheTtl(int $ttl): self
    {
        $this->cacheTtl = $ttl;

        return $this;
    }

    /**
     * Enable or disable caching for this service
     */
    public function enableCaching(bool $enable = true): self
    {
        $this->enableCaching = $enable;

        return $this;
    }

    /**
     * Set cache tags for this service
     */
    public function setCacheTags(array $tags): self
    {
        $this->cacheTags = $tags;

        return $this;
    }
}
