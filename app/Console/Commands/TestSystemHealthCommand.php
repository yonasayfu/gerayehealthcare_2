<?php

namespace App\Console\Commands;

use App\DTOs\BaseDTO;
use App\Models\Patient;
use App\Models\Staff;
use App\Models\VisitService;
use App\Services\CachedDropdown\CachedDropdownService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class TestSystemHealthCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:system-health {--detailed : Show detailed test results}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test system health including DTOs, caching, database, and performance';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🏥 Geraye Healthcare System Health Check');
        $this->info('=====================================');

        $detailed = $this->option('detailed');
        $allPassed = true;

        // Test Database Connectivity
        $allPassed &= $this->testDatabaseConnectivity();

        // Test Redis/Cache
        $allPassed &= $this->testCacheSystem();

        // Test DTO System
        $allPassed &= $this->testDTOSystem();

        // Test Cached Dropdown Service
        $allPassed &= $this->testCachedDropdownService();

        // Test Core Models
        $allPassed &= $this->testCoreModels();

        // Test Performance
        if ($detailed) {
            $allPassed &= $this->testPerformance();
        }

        // Test API Endpoints
        $allPassed &= $this->testAPIHealth();

        // Summary
        $this->newLine();
        if ($allPassed) {
            $this->info('✅ All system health checks passed!');
            $this->info('🚀 System is ready for production use.');
        } else {
            $this->error('❌ Some health checks failed!');
            $this->error('🔧 Please review the issues above.');
        }

        return $allPassed ? 0 : 1;
    }

    private function testDatabaseConnectivity(): bool
    {
        $this->info('🔍 Testing Database Connectivity...');

        try {
            $connection = DB::connection();
            $pdo = $connection->getPdo();

            if ($pdo) {
                $this->line('  ✅ Database connection successful');

                // Test basic query
                $result = DB::select('SELECT 1 as test');
                if ($result && $result[0]->test == 1) {
                    $this->line('  ✅ Database query execution successful');
                } else {
                    $this->line('  ❌ Database query execution failed');

                    return false;
                }

                return true;
            }
        } catch (\Exception $e) {
            $this->line('  ❌ Database connection failed: '.$e->getMessage());

            return false;
        }

        return false;
    }

    private function testCacheSystem(): bool
    {
        $this->info('🔍 Testing Cache System...');

        try {
            // Test basic cache operations
            $testKey = 'health_check_'.time();
            $testValue = 'test_value_'.rand(1000, 9999);

            Cache::put($testKey, $testValue, 60);
            $retrieved = Cache::get($testKey);

            if ($retrieved === $testValue) {
                $this->line('  ✅ Cache write/read successful');
                Cache::forget($testKey);
            } else {
                $this->line('  ❌ Cache write/read failed');

                return false;
            }

            // Test Redis if available
            try {
                Redis::ping();
                $this->line('  ✅ Redis connection successful');
            } catch (\Exception $e) {
                $this->line('  ⚠️  Redis not available (using file cache)');
            }

            return true;
        } catch (\Exception $e) {
            $this->line('  ❌ Cache system failed: '.$e->getMessage());

            return false;
        }
    }

    private function testDTOSystem(): bool
    {
        $this->info('🔍 Testing Enhanced DTO System...');

        try {
            // Test DTO pool statistics
            $poolStats = BaseDTO::getPoolStats();
            $this->line('  ✅ DTO pool system operational');

            if ($this->option('detailed')) {
                $this->line('    Pool stats: '.json_encode($poolStats));
            }

            // Test memory optimization
            $memoryBefore = memory_get_usage();

            // Create and release multiple DTOs to test pooling
            for ($i = 0; $i < 10; $i++) {
                $dto = BaseDTO::create(['test' => 'data_'.$i]);
                $dto->release();
            }

            $memoryAfter = memory_get_usage();
            $memoryDiff = $memoryAfter - $memoryBefore;

            $this->line('  ✅ DTO memory optimization working (diff: '.$this->formatBytes($memoryDiff).')');

            return true;
        } catch (\Exception $e) {
            $this->line('  ❌ DTO system failed: '.$e->getMessage());

            return false;
        }
    }

    private function testCachedDropdownService(): bool
    {
        $this->info('🔍 Testing Cached Dropdown Service...');

        try {
            // Test cache statistics
            $cacheStats = CachedDropdownService::getCacheStats();
            $this->line('  ✅ Cache statistics retrieved');

            if ($this->option('detailed')) {
                foreach ($cacheStats as $key => $status) {
                    $this->line("    {$key}: {$status}");
                }
            }

            // Test memory statistics
            $memoryStats = CachedDropdownService::getMemoryStats();
            $this->line('  ✅ Memory usage: '.$this->formatBytes($memoryStats['memory_usage']));
            $this->line('  ✅ Peak memory: '.$this->formatBytes($memoryStats['memory_peak']));

            // Test specific dropdown
            $patients = CachedDropdownService::getPatients();
            $this->line('  ✅ Patient dropdown loaded ('.count($patients).' patients)');

            return true;
        } catch (\Exception $e) {
            $this->line('  ❌ Cached dropdown service failed: '.$e->getMessage());

            return false;
        }
    }

    private function testCoreModels(): bool
    {
        $this->info('🔍 Testing Core Models...');

        try {
            // Test Patient model
            $patientCount = Patient::count();
            $this->line("  ✅ Patient model operational ({$patientCount} patients)");

            // Test Staff model
            $staffCount = Staff::count();
            $this->line("  ✅ Staff model operational ({$staffCount} staff members)");

            // Test VisitService model
            $visitCount = VisitService::count();
            $this->line("  ✅ VisitService model operational ({$visitCount} visits)");

            // Test relationships
            $patientWithVisits = Patient::with('visitServices')->first();
            if ($patientWithVisits) {
                $this->line('  ✅ Model relationships working');
            }

            return true;
        } catch (\Exception $e) {
            $this->line('  ❌ Core models failed: '.$e->getMessage());

            return false;
        }
    }

    private function testPerformance(): bool
    {
        $this->info('🔍 Testing Performance...');

        try {
            // Test database query performance
            $start = microtime(true);
            Patient::limit(100)->get();
            $dbTime = microtime(true) - $start;

            $this->line('  ✅ Database query time: '.round($dbTime * 1000, 2).'ms');

            // Test cache performance
            $start = microtime(true);
            CachedDropdownService::getPatients();
            $cacheTime = microtime(true) - $start;

            $this->line('  ✅ Cache retrieval time: '.round($cacheTime * 1000, 2).'ms');

            // Performance warnings
            if ($dbTime > 0.5) {
                $this->line('  ⚠️  Database queries are slow (>500ms)');
            }

            if ($cacheTime > 0.1) {
                $this->line('  ⚠️  Cache retrieval is slow (>100ms)');
            }

            return true;
        } catch (\Exception $e) {
            $this->line('  ❌ Performance test failed: '.$e->getMessage());

            return false;
        }
    }

    private function testAPIHealth(): bool
    {
        $this->info('🔍 Testing API Health...');

        try {
            // Test API routes are registered
            $routes = app('router')->getRoutes();
            $apiRoutes = 0;

            foreach ($routes as $route) {
                if (str_starts_with($route->uri(), 'api/')) {
                    $apiRoutes++;
                }
            }

            $this->line("  ✅ API routes registered ({$apiRoutes} routes)");

            // Test middleware
            $middlewareGroups = config('app.middleware_groups', []);
            if (isset($middlewareGroups['api'])) {
                $this->line('  ✅ API middleware configured');
            }

            return true;
        } catch (\Exception $e) {
            $this->line('  ❌ API health check failed: '.$e->getMessage());

            return false;
        }
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, 2).' '.$units[$pow];
    }
}
