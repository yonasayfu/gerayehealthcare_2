<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class QueryCacheMiddleware
{
    protected $cacheTtl = 600; // 10 minutes default cache

    protected $cacheableRoutes = [
        'admin.patients.index',
        'admin.staff.index',
        'admin.invoices.index',
        'admin.inventory-items.index',
        'admin.events.index',
        'admin.marketing-analytics.dashboard',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only cache GET requests for specific routes
        if (! $request->isMethod('GET') || ! $this->shouldCache($request)) {
            return $next($request);
        }

        $cacheKey = $this->generateCacheKey($request);

        // Check if response is cached
        if (Cache::has($cacheKey)) {
            $cachedResponse = Cache::get($cacheKey);

            // Add cache headers
            $response = response($cachedResponse['content'], $cachedResponse['status'])
                ->withHeaders($cachedResponse['headers']);
            $response->headers->set('X-Cache-Status', 'HIT');

            return $response;
        }

        // Enable query log to track database queries
        DB::enableQueryLog();

        $response = $next($request);

        // Get executed queries
        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        // Cache successful responses with reasonable query count
        if ($response->isSuccessful() && count($queries) > 2) {
            $this->cacheResponse($cacheKey, $response, count($queries));
        }

        // Add debug headers
        $response->headers->set('X-Cache-Status', 'MISS');
        $response->headers->set('X-Query-Count', count($queries));

        return $response;
    }

    /**
     * Determine if the request should be cached
     */
    protected function shouldCache(Request $request): bool
    {
        $routeName = $request->route()->getName();

        // Cache specific routes
        if (in_array($routeName, $this->cacheableRoutes)) {
            return true;
        }

        // Cache admin index routes
        if (str_starts_with($routeName, 'admin.') && str_ends_with($routeName, '.index')) {
            return true;
        }

        // Cache dashboard routes
        if (str_contains($routeName, 'dashboard')) {
            return true;
        }

        return false;
    }

    /**
     * Generate cache key for the request
     */
    protected function generateCacheKey(Request $request): string
    {
        $routeName = $request->route()->getName();
        $parameters = $request->query();

        // Sort parameters for consistent cache keys
        ksort($parameters);

        $keyData = [
            'route' => $routeName,
            'params' => $parameters,
            'user_id' => $request->user()?->id,
        ];

        return 'query_cache_'.md5(serialize($keyData));
    }

    /**
     * Cache the response
     */
    protected function cacheResponse(string $cacheKey, Response $response, int $queryCount): void
    {
        // Adjust cache TTL based on query complexity
        $ttl = $this->calculateCacheTtl($queryCount);

        $cacheData = [
            'content' => $response->getContent(),
            'status' => $response->getStatusCode(),
            'headers' => $response->headers->all(),
            'cached_at' => now()->toISOString(),
            'query_count' => $queryCount,
        ];

        Cache::put($cacheKey, $cacheData, $ttl);
    }

    /**
     * Calculate cache TTL based on query complexity
     */
    protected function calculateCacheTtl(int $queryCount): int
    {
        if ($queryCount > 10) {
            return 1800; // 30 minutes for heavy queries
        } elseif ($queryCount > 5) {
            return 900;  // 15 minutes for medium queries
        } else {
            return $this->cacheTtl; // 10 minutes for light queries
        }
    }
}
