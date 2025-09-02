<?php

namespace App\Services;

use Illuminate\Http\Request;

class TestService
{
    /**
     * Test data
     */
    private array $testData = [
        ['id' => 1, 'name' => 'Test Item 1', 'description' => 'This is test item 1'],
        ['id' => 2, 'name' => 'Test Item 2', 'description' => 'This is test item 2'],
        ['id' => 3, 'name' => 'Test Item 3', 'description' => 'This is test item 3'],
    ];

    /**
     * Get all test data
     */
    public function getAllTestData(Request $request): array
    {
        $data = $this->testData;

        // Apply search filter
        if ($request->has('search')) {
            $search = $request->input('search');
            $data = array_filter($data, function ($item) use ($search) {
                return stripos($item['name'], $search) !== false ||
                stripos($item['description'], $search) !== false;
            });
        }

        // Apply sorting
        if ($request->has('sort')) {
            $sort = $request->input('sort');
            $direction = $request->input('direction', 'asc');

            if (in_array($sort, ['id', 'name'])) {
                usort($data, function ($a, $b) use ($sort, $direction) {
                    $result = $a[$sort] <=> $b[$sort];

                    return $direction === 'desc' ? -$result : $result;
                });
            }
        }

        return array_values($data);
    }

    /**
     * Find test data by ID
     */
    public function findTestDataById(int $id): ?array
    {
        foreach ($this->testData as $item) {
            if ($item['id'] == $id) {
                return $item;
            }
        }

        return null;
    }
}
