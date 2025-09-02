<?php

namespace App\Http\Controllers;

use App\Services\CachedDropdownService;
use Illuminate\Http\Request;

class TestController extends OptimizedBaseController
{
    /**
     * Test the base controller functionality
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Test caching functionality
        $cachedData = $this->remember('test_key', function () {
            return [
                'message' => 'This data was cached',
                'timestamp' => now()->toISOString(),
            ];
        }, 60);

        // Test dropdown service
        $users = CachedDropdownService::getUsers();

        return $this->success([
            'cached_data' => $cachedData,
            'users' => $users,
            'pagination' => $this->getPaginationParams($request),
            'sort' => $this->getSortParams($request),
        ], 'Test data retrieved successfully');
    }

    /**
     * Test error handling
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function error(string $message = 'An error occurred', int $code = 400, array $errors = [])
    {
        return $this->error('This is a test error', 400, [
            'test_error' => 'This is a test error detail',
        ]);
    }
}
