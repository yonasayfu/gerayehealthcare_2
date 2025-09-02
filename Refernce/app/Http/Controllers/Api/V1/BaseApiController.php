<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BaseApiController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Default pagination size for API responses
     */
    protected int $perPage = 15;

    /**
     * API version
     */
    protected string $version = 'v1';

    /**
     * Create a new API controller instance
     *
     * @return void
     */
    public function __construct()
    {
        // Apply API middleware
        $this->middleware('api');
    }

    /**
     * Return a successful API response
     *
     * @param  mixed  $data
     */
    protected function success($data = null, string $message = 'Operation successful', int $code = 200, array $meta = []): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        if (! empty($meta)) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $code);
    }

    /**
     * Return an error API response
     */
    protected function error(string $message = 'An error occurred', int $code = 400, array $errors = [], array $meta = []): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (! empty($errors)) {
            $response['errors'] = $errors;
        }

        if (! empty($meta)) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $code);
    }

    /**
     * Return a paginated API response
     *
     * @param  \Illuminate\Contracts\Pagination\LengthAwarePaginator  $paginator
     */
    protected function paginated($paginator, string $message = 'Data retrieved successfully'): JsonResponse
    {
        return $this->success(
            $paginator->items(),
            $message,
            200,
            [
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                    'from' => $paginator->firstItem(),
                    'to' => $paginator->lastItem(),
                ],
                'links' => [
                    'first' => $paginator->url(1),
                    'last' => $paginator->url($paginator->lastPage()),
                    'prev' => $paginator->previousPageUrl(),
                    'next' => $paginator->nextPageUrl(),
                ],
            ]
        );
    }

    /**
     * Get pagination parameters from request
     */
    protected function getPaginationParams(Request $request): array
    {
        return [
            'page' => $request->input('page', 1),
            'per_page' => min($request->input('per_page', $this->perPage), 100), // Limit to 100 per page
        ];
    }

    /**
     * Get sorting parameters from request
     */
    protected function getSortParams(Request $request, string $defaultColumn = 'id', string $defaultDirection = 'asc'): array
    {
        return [
            'sort' => $request->input('sort', $defaultColumn),
            'direction' => strtolower($request->input('direction', $defaultDirection)),
        ];
    }

    /**
     * Get search parameters from request
     */
    protected function getSearchParam(Request $request): ?string
    {
        return $request->input('search');
    }

    /**
     * Get filter parameters from request
     */
    protected function getFilterParams(Request $request): array
    {
        $filters = $request->input('filters', []);

        if (is_string($filters)) {
            // If filters is a JSON string, decode it
            $filters = json_decode($filters, true) ?: [];
        }

        return is_array($filters) ? $filters : [];
    }

    /**
     * Apply rate limiting to API endpoint
     */
    protected function rateLimit(string $key, int $maxAttempts = 60, int $decayMinutes = 1): void
    {
        // This would typically use Laravel's rate limiter
        // Implementation depends on your specific requirements
    }

    /**
     * Validate API request data
     */
    protected function validateApiRequest(Request $request, array $rules, array $messages = []): array
    {
        try {
            return $request->validate($rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->errors();

            // Format validation errors for API response
            $formattedErrors = [];
            foreach ($errors as $field => $messages) {
                $formattedErrors[$field] = is_array($messages) ? $messages : [$messages];
            }

            return $this->error('Validation failed', 422, $formattedErrors)->getData(true);
        }
    }
}
