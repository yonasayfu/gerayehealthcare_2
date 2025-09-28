<?php

if (! app()->environment('local')) {
    return;
}

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/performance-test', function (Request $request) {
    $startTime = microtime(true);
    $queryCount = 0;
    $queryStartTime = microtime(true);

    // Listen to queries
    DB::listen(function ($query) use (&$queryCount) {
        $queryCount++;
    });

    $results = [];

    // Test 1: Simple database connection
    $testStart = microtime(true);
    try {
        DB::connection()->getPdo();
        $results['database_connection'] = [
            'status' => 'success',
            'time' => round((microtime(true) - $testStart) * 1000, 2).' ms',
        ];
    } catch (Exception $e) {
        $results['database_connection'] = [
            'status' => 'error',
            'error' => $e->getMessage(),
            'time' => round((microtime(true) - $testStart) * 1000, 2).' ms',
        ];
    }

    // Test 2: Basic model query
    $testStart = microtime(true);
    try {
        $patientCount = Patient::count();
        $results['basic_query'] = [
            'status' => 'success',
            'result' => "Found {$patientCount} patients",
            'time' => round((microtime(true) - $testStart) * 1000, 2).' ms',
        ];
    } catch (Exception $e) {
        $results['basic_query'] = [
            'status' => 'error',
            'error' => $e->getMessage(),
            'time' => round((microtime(true) - $testStart) * 1000, 2).' ms',
        ];
    }

    // Test 3: Complex query with relationships
    $testStart = microtime(true);
    try {
        $patients = Patient::with(['invoices', 'appointments'])
            ->limit(10)
            ->get();
        $results['complex_query'] = [
            'status' => 'success',
            'result' => "Loaded {$patients->count()} patients with relationships",
            'time' => round((microtime(true) - $testStart) * 1000, 2).' ms',
        ];
    } catch (Exception $e) {
        $results['complex_query'] = [
            'status' => 'error',
            'error' => $e->getMessage(),
            'time' => round((microtime(true) - $testStart) * 1000, 2).' ms',
        ];
    }

    // Test 4: Cache performance
    $testStart = microtime(true);
    try {
        cache()->put('performance_test', 'test_value', 60);
        $cachedValue = cache()->get('performance_test');
        $results['cache_performance'] = [
            'status' => 'success',
            'result' => "Cache working: {$cachedValue}",
            'time' => round((microtime(true) - $testStart) * 1000, 2).' ms',
        ];
    } catch (Exception $e) {
        $results['cache_performance'] = [
            'status' => 'error',
            'error' => $e->getMessage(),
            'time' => round((microtime(true) - $testStart) * 1000, 2).' ms',
        ];
    }

    // Test 5: Session performance
    $testStart = microtime(true);
    try {
        session(['performance_test' => 'test_session_value']);
        $sessionValue = session('performance_test');
        $results['session_performance'] = [
            'status' => 'success',
            'result' => "Session working: {$sessionValue}",
            'time' => round((microtime(true) - $testStart) * 1000, 2).' ms',
        ];
    } catch (Exception $e) {
        $results['session_performance'] = [
            'status' => 'error',
            'error' => $e->getMessage(),
            'time' => round((microtime(true) - $testStart) * 1000, 2).' ms',
        ];
    }

    $totalTime = microtime(true) - $startTime;
    $queryTime = microtime(true) - $queryStartTime;

    return response()->json([
        'total_execution_time' => round($totalTime * 1000, 2).' ms',
        'total_queries' => $queryCount,
        'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2).' MB',
        'peak_memory' => round(memory_get_peak_usage(true) / 1024 / 1024, 2).' MB',
        'php_version' => PHP_VERSION,
        'laravel_version' => app()->version(),
        'environment' => app()->environment(),
        'tests' => $results,
        'recommendations' => $this->getPerformanceRecommendations($totalTime, $queryCount, $results),
    ]);
})->name('performance.test');

function getPerformanceRecommendations($totalTime, $queryCount, $results)
{
    $recommendations = [];

    if ($totalTime > 0.1) {
        $recommendations[] = 'Total execution time is '.round($totalTime * 1000, 2).'ms. Consider optimizing slow operations.';
    }

    if ($queryCount > 10) {
        $recommendations[] = "High query count ({$queryCount}). Consider using eager loading or caching.";
    }

    $dbTime = isset($results['database_connection']['time']) ?
        (float) str_replace(' ms', '', $results['database_connection']['time']) : 0;

    if ($dbTime > 50) {
        $recommendations[] = "Database connection is slow ({$dbTime}ms). Check database server performance.";
    }

    if (empty($recommendations)) {
        $recommendations[] = 'Backend performance looks good. Check frontend optimization and network conditions.';
    }

    return $recommendations;
}
