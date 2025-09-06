<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

// Temporary cache testing routes - Remove in production
Route::middleware(['web'])->prefix('cache-test')->group(function () {

    // Test basic caching
    Route::get('/basic', function () {
        $start = microtime(true);

        $data = Cache::remember('test_key', 300, function () {
            // Simulate expensive operation
            sleep(1);

            return [
                'message' => 'This data was cached',
                'timestamp' => now()->toDateTimeString(),
                'random' => rand(1000, 9999),
            ];
        });

        $end = microtime(true);
        $executionTime = ($end - $start) * 1000; // Convert to milliseconds

        return response()->json([
            'cached_data' => $data,
            'execution_time_ms' => round($executionTime, 2),
            'cache_driver' => config('cache.default'),
            'is_cached' => $executionTime < 50, // If under 50ms, likely cached
        ]);
    });

    // Test cache clearing
    Route::delete('/clear', function () {
        Cache::forget('test_key');

        return response()->json(['message' => 'Cache cleared']);
    });

    // Test service layer caching
    Route::get('/service-test', function () {
        $start = microtime(true);

        // Test our OptimizedBaseService caching
        $cacheKey = 'service_test_'.md5('test_params');
        $data = Cache::remember($cacheKey, 300, function () {
            // Simulate database query
            return \App\Models\User::take(5)->get(['id', 'name', 'email']);
        });

        $end = microtime(true);
        $executionTime = ($end - $start) * 1000;

        return response()->json([
            'users' => $data,
            'execution_time_ms' => round($executionTime, 2),
            'cache_key' => $cacheKey,
            'is_cached' => $executionTime < 50,
        ]);
    });

    // Performance comparison test
    Route::get('/performance-compare', function (Request $request) {
        $iterations = $request->get('iterations', 100);

        // Test without cache
        $startNoCache = microtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            \App\Models\User::count();
        }
        $endNoCache = microtime(true);
        $noCacheTime = ($endNoCache - $startNoCache) * 1000;

        // Test with cache
        $startWithCache = microtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            Cache::remember('user_count', 60, function () {
                return \App\Models\User::count();
            });
        }
        $endWithCache = microtime(true);
        $withCacheTime = ($endWithCache - $startWithCache) * 1000;

        $improvement = (($noCacheTime - $withCacheTime) / $noCacheTime) * 100;

        return response()->json([
            'iterations' => $iterations,
            'without_cache_ms' => round($noCacheTime, 2),
            'with_cache_ms' => round($withCacheTime, 2),
            'performance_improvement' => round($improvement, 2).'%',
            'cache_driver' => config('cache.default'),
        ]);
    });
});
