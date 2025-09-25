<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PerformanceBenchmarkCommand extends Command
{
    protected $signature = 'performance:benchmark {--iterations=10 : Number of iterations to run}';

    protected $description = 'Run comprehensive performance benchmarks for Clean Architecture optimizations';

    public function handle()
    {
        $iterations = (int) $this->option('iterations');

        $this->info('🚀 Starting Clean Architecture Performance Benchmark');
        $this->info("Running {$iterations} iterations for each test...\n");

        $results = [
            'database' => $this->benchmarkDatabase(),
            'cache' => $this->benchmarkCache(),
            'services' => $this->benchmarkServices($iterations),
            'memory' => $this->benchmarkMemoryUsage(),
            'frontend' => $this->benchmarkFrontendAssets(),
        ];

        $this->displayResults($results);
        $this->generateReport($results);

        return Command::SUCCESS;
    }

    protected function benchmarkDatabase(): array
    {
        $this->info('📊 Benchmarking Database Performance...');

        $start = microtime(true);

        // Test database indexes
        $indexTests = [
            'patients_search' => "SELECT COUNT(*) FROM patients WHERE full_name LIKE '%John%'",
            'staff_department' => "SELECT COUNT(*) FROM staff WHERE department = 'Medical'",
            'invoices_status' => "SELECT COUNT(*) FROM invoices WHERE status = 'Pending'",
            'inventory_category' => "SELECT COUNT(*) FROM inventory_items WHERE item_category = 'Equipment'",
        ];

        $indexResults = [];
        foreach ($indexTests as $name => $query) {
            $start = microtime(true);
            DB::select($query);
            $indexResults[$name] = round((microtime(true) - $start) * 1000, 2);
        }

        $this->info("✅ Database benchmarks completed\n");

        return [
            'index_performance' => $indexResults,
            'total_time' => round((microtime(true) - $start) * 1000, 2),
        ];
    }

    protected function benchmarkCache(): array
    {
        $this->info('🗄️ Benchmarking Cache Performance...');

        $cacheTests = [
            'write' => $this->benchmarkCacheWrite(),
            'read' => $this->benchmarkCacheRead(),
            'pattern_clear' => $this->benchmarkCachePatternClear(),
        ];

        $this->info("✅ Cache benchmarks completed\n");

        return $cacheTests;
    }

    protected function benchmarkCacheWrite(): float
    {
        $start = microtime(true);

        for ($i = 0; $i < 100; $i++) {
            Cache::put("benchmark_key_{$i}", ['data' => str_repeat('x', 1000)], 3600);
        }

        return round((microtime(true) - $start) * 1000, 2);
    }

    protected function benchmarkCacheRead(): float
    {
        $start = microtime(true);

        for ($i = 0; $i < 100; $i++) {
            Cache::get("benchmark_key_{$i}");
        }

        return round((microtime(true) - $start) * 1000, 2);
    }

    protected function benchmarkCachePatternClear(): float
    {
        $start = microtime(true);
        Cache::flush(); // Simulate pattern clearing

        return round((microtime(true) - $start) * 1000, 2);
    }

    protected function benchmarkServices(int $iterations): array
    {
        $this->info('⚡ Benchmarking Optimized Services...');

        $services = [
            'PatientService' => \App\Services\Optimized\Patient\PatientService::class,
            'StaffService' => \App\Services\Optimized\StaffService::class,
            'InventoryService' => \App\Services\Optimized\InventoryItemService::class,
            'InvoiceService' => \App\Services\Optimized\Invoice\InvoiceService::class,
            'MarketingAnalyticsService' => \App\Services\MarketingAnalytics\MarketingAnalyticsService::class,
            'EventService' => \App\Services\Optimized\EventService::class,
        ];

        $results = [];
        foreach ($services as $name => $serviceClass) {
            $this->line("  Testing {$name}...");
            $results[$name] = $this->benchmarkService($serviceClass, $iterations);
        }

        $this->info("✅ Service benchmarks completed\n");

        return $results;
    }

    protected function benchmarkService(string $serviceClass, int $iterations): array
    {
        $times = [];
        $memoryUsage = [];

        for ($i = 0; $i < $iterations; $i++) {
            $startMemory = memory_get_usage(true);
            $start = microtime(true);

            // Simulate service operations
            $service = app($serviceClass);
            $request = request();

            // Test getAll method if it exists
            if (method_exists($service, 'getAll')) {
                $service->getAll($request);
            }

            $times[] = round((microtime(true) - $start) * 1000, 2);
            $memoryUsage[] = memory_get_usage(true) - $startMemory;
        }

        return [
            'avg_time_ms' => round(array_sum($times) / count($times), 2),
            'min_time_ms' => min($times),
            'max_time_ms' => max($times),
            'avg_memory_mb' => round(array_sum($memoryUsage) / count($memoryUsage) / 1024 / 1024, 2),
        ];
    }

    protected function benchmarkMemoryUsage(): array
    {
        $this->info('🧠 Analyzing Memory Usage...');

        $start = memory_get_usage(true);
        $peak = memory_get_peak_usage(true);

        // Test DTO object pooling
        $dtoStart = memory_get_usage(true);
        for ($i = 0; $i < 1000; $i++) {
            \App\DTOs\CreatePatientDTO::create([
                'full_name' => "Test Patient {$i}",
                'phone' => '123456789',
                'age' => 30,
                'gender' => 'Male',
            ]);
        }
        $dtoMemory = memory_get_usage(true) - $dtoStart;

        $this->info("✅ Memory analysis completed\n");

        return [
            'current_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
            'peak_mb' => round($peak / 1024 / 1024, 2),
            'dto_pooling_mb' => round($dtoMemory / 1024 / 1024, 2),
        ];
    }

    protected function benchmarkFrontendAssets(): array
    {
        $this->info('🎨 Analyzing Frontend Assets...');

        $manifestPath = public_path('build/manifest.json');
        if (!file_exists($manifestPath)) {
            return ['error' => 'Build manifest not found'];
        }

        $manifest = json_decode(file_get_contents($manifestPath), true);
        $assetSizes = [];

        foreach ($manifest as $key => $asset) {
            if (isset($asset['file'])) {
                $filePath = public_path('build/' . $asset['file']);
                if (file_exists($filePath)) {
                    $assetSizes[$key] = round(filesize($filePath) / 1024, 2); // KB
                }
            }
        }

        $this->info("✅ Frontend analysis completed\n");

        return [
            'asset_count' => count($assetSizes),
            'total_size_kb' => array_sum($assetSizes),
            'largest_asset' => max($assetSizes),
            'chunks' => array_filter($assetSizes, fn($size, $key) => str_contains($key, 'chunk'), ARRAY_FILTER_USE_BOTH),
        ];
    }

    protected function displayResults(array $results): void
    {
        $this->info('📋 PERFORMANCE BENCHMARK RESULTS');
        $this->info("=====================================\n");

        // Database Results
        $this->info('🗄️ Database Performance:');
        foreach ($results['database']['index_performance'] as $test => $time) {
            $this->line("  {$test}: {$time}ms");
        }
        $this->newLine();

        // Cache Results
        $this->info('💾 Cache Performance:');
        $this->line("  Write (100 ops): {$results['cache']['write']}ms");
        $this->line("  Read (100 ops): {$results['cache']['read']}ms");
        $this->line("  Pattern Clear: {$results['cache']['pattern_clear']}ms");
        $this->newLine();

        // Service Results
        $this->info('⚡ Service Performance:');
        foreach ($results['services'] as $service => $metrics) {
            $this->line("  {$service}:");
            $this->line("    Avg Time: {$metrics['avg_time_ms']}ms");
            $this->line("    Memory: {$metrics['avg_memory_mb']}MB");
        }
        $this->newLine();

        // Memory Results
        $this->info('🧠 Memory Usage:');
        $this->line("  Current: {$results['memory']['current_mb']}MB");
        $this->line("  Peak: {$results['memory']['peak_mb']}MB");
        $this->line("  DTO Pooling: {$results['memory']['dto_pooling_mb']}MB");
        $this->newLine();

        // Frontend Results
        if (!isset($results['frontend']['error'])) {
            $this->info('🎨 Frontend Assets:');
            $this->line("  Asset Count: {$results['frontend']['asset_count']}");
            $this->line("  Total Size: {$results['frontend']['total_size_kb']}KB");
            $this->line('  Chunk Count: ' . count($results['frontend']['chunks']));
        }
    }

    protected function generateReport(array $results): void
    {
        $reportPath = storage_path('logs/performance_benchmark_' . date('Y-m-d_H-i-s') . '.json');

        $report = [
            'timestamp' => now()->toISOString(),
            'environment' => app()->environment(),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'results' => $results,
            'recommendations' => $this->generateRecommendations($results),
        ];

        file_put_contents($reportPath, json_encode($report, JSON_PRETTY_PRINT));

        $this->info("📊 Detailed report saved to: {$reportPath}");
    }

    protected function generateRecommendations(array $results): array
    {
        $recommendations = [];

        // Database recommendations
        $avgIndexTime = array_sum($results['database']['index_performance']) / count($results['database']['index_performance']);
        if ($avgIndexTime > 50) {
            $recommendations[] = "Consider adding more specific database indexes - average query time is {$avgIndexTime}ms";
        }

        // Cache recommendations
        if ($results['cache']['read'] > $results['cache']['write']) {
            $recommendations[] = 'Cache read performance is slower than write - consider cache driver optimization';
        }

        // Memory recommendations
        if ($results['memory']['peak_mb'] > 256) {
            $recommendations[] = "High memory usage detected ({$results['memory']['peak_mb']}MB) - consider memory optimization";
        }

        // Service performance recommendations
        foreach ($results['services'] as $service => $metrics) {
            if ($metrics['avg_time_ms'] > 100) {
                $recommendations[] = "{$service} average response time is {$metrics['avg_time_ms']}ms - consider further optimization";
            }
        }

        return $recommendations;
    }
}
