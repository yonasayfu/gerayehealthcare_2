<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;

class TestApiController extends BaseApiController
{
    /**
     * Test the base API controller functionality
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Get pagination parameters
        $pagination = $this->getPaginationParams($request);

        // Get sorting parameters
        $sort = $this->getSortParams($request);

        // Get search parameter
        $search = $this->getSearchParam($request);

        // Create some test data
        $data = [
            ['id' => 1, 'name' => 'Test Item 1', 'description' => 'This is test item 1'],
            ['id' => 2, 'name' => 'Test Item 2', 'description' => 'This is test item 2'],
            ['id' => 3, 'name' => 'Test Item 3', 'description' => 'This is test item 3'],
        ];

        // Apply search filter if provided
        if ($search) {
            $data = array_filter($data, function ($item) use ($search) {
                return stripos($item['name'], $search) !== false ||
                stripos($item['description'], $search) !== false;
            });
        }

        // Apply sorting
        if (in_array($sort['sort'], ['id', 'name'])) {
            usort($data, function ($a, $b) use ($sort) {
                $result = $a[$sort['sort']] <=> $b[$sort['sort']];
                return $sort['direction'] === 'desc' ? -$result : $result;
            });
        }

        // Apply pagination
        $total = count($data);
        $offset = ($pagination['page'] - 1) * $pagination['per_page'];
        $data = array_slice($data, $offset, $pagination['per_page']);

        // Return paginated response
        return $this->success($data, 'Test data retrieved successfully', 200, [
            'pagination' => [
                'current_page' => $pagination['page'],
                'last_page' => ceil($total / $pagination['per_page']),
                'per_page' => $pagination['per_page'],
                'total' => $total,
            ],
        ]);
    }

    /**
     * Test error handling in API
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function error()
    {
        return $this->error('This is a test API error', 400, [
            'test_error' => 'This is a test error detail',
        ]);
    }
}
