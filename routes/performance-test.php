<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\OptimizedPatientController;
use Illuminate\Http\Request;

// Performance comparison routes (remove in production)
Route::middleware(['auth', 'verified'])->prefix('performance-test')->group(function () {
    
    // Test original PatientController
    Route::get('/original-patients', function (Request $request) {
        $start = microtime(true);
        
        try {
            $controller = app(PatientController::class);
            $response = $controller->index($request);
            
            $end = microtime(true);
            $executionTime = ($end - $start) * 1000;
            
            // Get the patient data from the response
            $data = $response->toResponse($request)->getData(true);
            
            return response()->json([
                'controller' => 'Original PatientController',
                'execution_time_ms' => round($executionTime, 2),
                'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
                'patient_count' => count($data['props']['patients']['data'] ?? []),
                'total_patients' => $data['props']['patients']['total'] ?? 0,
                'cache_used' => false,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'controller' => 'Original PatientController'
            ], 500);
        }
    });
    
    // Test optimized PatientController
    Route::get('/optimized-patients', function (Request $request) {
        $start = microtime(true);
        
        try {
            $controller = app(OptimizedPatientController::class);
            $response = $controller->index($request);
            
            $end = microtime(true);
            $executionTime = ($end - $start) * 1000;
            
            // Get the patient data from the response
            $data = $response->toResponse($request)->getData(true);
            
            return response()->json([
                'controller' => 'Optimized PatientController',
                'execution_time_ms' => round($executionTime, 2),
                'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
                'patient_count' => count($data['props']['patients']['data'] ?? []),
                'total_patients' => $data['props']['patients']['total'] ?? 0,
                'statistics' => $data['props']['statistics'] ?? null,
                'cache_used' => true,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'controller' => 'Optimized PatientController'
            ], 500);
        }
    });
    
    // Performance comparison
    Route::get('/compare-patients', function (Request $request) {
        $iterations = $request->get('iterations', 5);
        $results = [];
        
        // Test both controllers multiple times
        for ($i = 0; $i < $iterations; $i++) {
            // Test original
            $start = microtime(true);
            $originalController = app(PatientController::class);
            $originalResponse = $originalController->index($request);
            $originalTime = (microtime(true) - $start) * 1000;
            
            // Test optimized
            $start = microtime(true);
            $optimizedController = app(OptimizedPatientController::class);
            $optimizedResponse = $optimizedController->index($request);
            $optimizedTime = (microtime(true) - $start) * 1000;
            
            $results[] = [
                'iteration' => $i + 1,
                'original_ms' => round($originalTime, 2),
                'optimized_ms' => round($optimizedTime, 2),
                'improvement_ms' => round($originalTime - $optimizedTime, 2),
                'improvement_percent' => round((($originalTime - $optimizedTime) / $originalTime) * 100, 2),
            ];
        }
        
        // Calculate averages
        $avgOriginal = array_sum(array_column($results, 'original_ms')) / $iterations;
        $avgOptimized = array_sum(array_column($results, 'optimized_ms')) / $iterations;
        $avgImprovement = (($avgOriginal - $avgOptimized) / $avgOriginal) * 100;
        
        return response()->json([
            'iterations' => $iterations,
            'results' => $results,
            'averages' => [
                'original_ms' => round($avgOriginal, 2),
                'optimized_ms' => round($avgOptimized, 2),
                'improvement_percent' => round($avgImprovement, 2),
                'improvement_description' => $avgImprovement > 0 ? 'Optimized is faster' : 'Original is faster'
            ],
            'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
            'cache_driver' => config('cache.default'),
        ]);
    });
    
    // Test original StaffController
    Route::get('/original-staff', function (Request $request) {
        $start = microtime(true);
        
        try {
            $controller = app(\App\Http\Controllers\Admin\StaffController::class);
            $response = $controller->index($request);
            
            $end = microtime(true);
            $executionTime = ($end - $start) * 1000;
            
            // Get the staff data from the response
            $data = $response->toResponse($request)->getData(true);
            
            return response()->json([
                'controller' => 'Original StaffController',
                'execution_time_ms' => round($executionTime, 2),
                'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
                'staff_count' => count($data['props']['staff']['data'] ?? []),
                'total_staff' => $data['props']['staff']['total'] ?? 0,
                'cache_used' => false,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'controller' => 'Original StaffController'
            ], 500);
        }
    });
    
    // Test optimized StaffController
    Route::get('/optimized-staff', function (Request $request) {
        $start = microtime(true);
        
        try {
            $controller = app(\App\Http\Controllers\Admin\OptimizedStaffController::class);
            $response = $controller->index($request);
            
            $end = microtime(true);
            $executionTime = ($end - $start) * 1000;
            
            // Get the staff data from the response
            $data = $response->toResponse($request)->getData(true);
            
            return response()->json([
                'controller' => 'Optimized StaffController',
                'execution_time_ms' => round($executionTime, 2),
                'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
                'staff_count' => count($data['props']['staff']['data'] ?? []),
                'total_staff' => $data['props']['staff']['total'] ?? 0,
                'statistics' => $data['props']['statistics'] ?? null,
                'cache_used' => true,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'controller' => 'Optimized StaffController'
            ], 500);
        }
    });
    
    // Performance comparison for staff
    Route::get('/compare-staff', function (Request $request) {
        $iterations = $request->get('iterations', 5);
        $results = [];
        
        // Test both controllers multiple times
        for ($i = 0; $i < $iterations; $i++) {
            // Test original
            $start = microtime(true);
            $originalController = app(\App\Http\Controllers\Admin\StaffController::class);
            $originalResponse = $originalController->index($request);
            $originalTime = (microtime(true) - $start) * 1000;
            
            // Test optimized
            $start = microtime(true);
            $optimizedController = app(\App\Http\Controllers\Admin\OptimizedStaffController::class);
            $optimizedResponse = $optimizedController->index($request);
            $optimizedTime = (microtime(true) - $start) * 1000;
            
            $results[] = [
                'iteration' => $i + 1,
                'original_ms' => round($originalTime, 2),
                'optimized_ms' => round($optimizedTime, 2),
                'improvement_ms' => round($originalTime - $optimizedTime, 2),
                'improvement_percent' => round((($originalTime - $optimizedTime) / $originalTime) * 100, 2),
            ];
        }
        
        // Calculate averages
        $avgOriginal = array_sum(array_column($results, 'original_ms')) / $iterations;
        $avgOptimized = array_sum(array_column($results, 'optimized_ms')) / $iterations;
        $avgImprovement = (($avgOriginal - $avgOptimized) / $avgOriginal) * 100;
        
        return response()->json([
            'iterations' => $iterations,
            'results' => $results,
            'averages' => [
                'original_ms' => round($avgOriginal, 2),
                'optimized_ms' => round($avgOptimized, 2),
                'improvement_percent' => round($avgImprovement, 2),
                'improvement_description' => $avgImprovement > 0 ? 'Optimized is faster' : 'Original is faster'
            ],
            'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
            'cache_driver' => config('cache.default'),
        ]);
    });

    // Invoice Performance Comparison Routes
    Route::group(['prefix' => 'invoices'], function () {
        // Original vs Optimized Invoice Index
        Route::get('/original', [\App\Http\Controllers\Admin\InvoiceController::class, 'index'])
            ->name('performance.invoices.original');
        Route::get('/optimized', [\App\Http\Controllers\Admin\OptimizedInvoiceController::class, 'index'])
            ->name('performance.invoices.optimized');

        // Invoice Creation Performance
        Route::get('/create/original', [\App\Http\Controllers\Admin\InvoiceController::class, 'create'])
            ->name('performance.invoices.create.original');
        Route::get('/create/optimized', [\App\Http\Controllers\Admin\OptimizedInvoiceController::class, 'create'])
            ->name('performance.invoices.create.optimized');

        // Financial Dashboard Data
        Route::get('/dashboard/original', function() {
            $start = microtime(true);
            $data = [
                'total_invoices' => \App\Models\Invoice::count(),
                'total_revenue' => \App\Models\Invoice::sum('grand_total'),
                'pending_amount' => \App\Models\Invoice::where('status', 'Pending')->sum('amount'),
            ];
            $time = microtime(true) - $start;
            return response()->json(['data' => $data, 'execution_time' => $time]);
        })->name('performance.invoices.dashboard.original');
        
        Route::get('/dashboard/optimized', [\App\Http\Controllers\Admin\OptimizedInvoiceController::class, 'dashboardData'])
            ->name('performance.invoices.dashboard.optimized');

        // Incoming Billables Performance
        Route::get('/incoming/original', [\App\Http\Controllers\Admin\InvoiceController::class, 'incoming'])
            ->name('performance.invoices.incoming.original');
        Route::get('/incoming/optimized', [\App\Http\Controllers\Admin\OptimizedInvoiceController::class, 'incoming'])
            ->name('performance.invoices.incoming.optimized');
    });
});