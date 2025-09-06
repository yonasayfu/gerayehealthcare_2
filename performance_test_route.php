<?php

// Add this to routes/web.php for testing purposes only
Route::get('/performance-test', function () {
    $start = microtime(true);

    // Test database queries
    $queries = [];
    DB::listen(function ($query) use (&$queries) {
        $queries[] = [
            'sql' => $query->sql,
            'bindings' => $query->bindings,
            'time' => $query->time,
        ];
    });

    // Simulate typical page load
    $patients = \App\Models\Patient::with(['employeeInsuranceRecord.policy.insuranceCompany'])
        ->paginate(5);

    $staff = \App\Models\Staff::with(['user', 'assignments.patient'])
        ->paginate(5);

    $inventoryItems = \App\Models\InventoryItem::with(['supplier'])
        ->paginate(5);

    $end = microtime(true);

    return response()->json([
        'total_time' => round(($end - $start) * 1000, 2).'ms',
        'total_queries' => count($queries),
        'queries' => $queries,
        'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2).'MB',
        'peak_memory' => round(memory_get_peak_usage(true) / 1024 / 1024, 2).'MB',
        'patient_count' => $patients->total(),
        'staff_count' => $staff->total(),
        'inventory_count' => $inventoryItems->total(),
    ]);
})->name('performance.test');
