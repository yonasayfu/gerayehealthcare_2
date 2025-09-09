<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\OptimizedBaseController;
use App\Services\GlobalSearchService;
use Illuminate\Http\Request;

class GlobalSearchController extends OptimizedBaseController
{
    protected GlobalSearchService $globalSearchService;

    public function __construct(GlobalSearchService $globalSearchService)
    {
        $this->globalSearchService = $globalSearchService;
        $this->middleware('auth');
        $this->middleware('can:view-admin-dashboard');
    }

    /**
     * Perform global search
     */
    public function search(Request $request)
    {
        try {
            $request->validate([
                'query' => 'required|string|min:2|max:255',
                'entities' => 'array',
                'entities.*' => 'string|in:users,staff,messages',
                'limit' => 'integer|min:1|max:50',
            ]);

            $query = $request->get('query');
            $entities = $request->get('entities', ['users', 'staff']);
            $limit = $request->get('limit', 20);

            $results = $this->globalSearchService->search($query, $entities, $limit);

            return $this->success([
                'results' => $results,
                'query' => $query,
                'total_results' => count($results),
                'searched_entities' => $entities,
            ], 'Search completed successfully');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Search failed');
        }
    }

    /**
     * Get search suggestions
     */
    public function suggestions(Request $request)
    {
        try {
            $request->validate([
                'query' => 'required|string|min:1|max:255',
                'entity' => 'string|in:users,staff,messages',
                'limit' => 'integer|min:1|max:10',
            ]);

            $query = $request->get('query');
            $entity = $request->get('entity');
            $limit = $request->get('limit', 5);

            $suggestions = $this->globalSearchService->getSuggestions($query, $entity, $limit);

            return $this->success([
                'suggestions' => $suggestions,
                'query' => $query,
            ], 'Suggestions retrieved successfully');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to get suggestions');
        }
    }

    /**
     * Get search statistics
     */
    public function getStats(Request $request)
    {
        try {
            $stats = $this->globalSearchService->getSearchStats();

            return $this->success($stats, 'Search statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to get search statistics');
        }
    }

    /**
     * Get available search entities
     */
    public function getEntities(Request $request)
    {
        try {
            $entities = $this->globalSearchService->getAvailableEntities();

            return $this->success([
                'entities' => $entities,
            ], 'Available entities retrieved successfully');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to get available entities');
        }
    }
}
