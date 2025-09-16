<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\EventRecommendation\EventRecommendationService;
use App\Services\Validation\Rules\EventRecommendationRules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EventRecommendationController extends Controller
{
    public function __construct(private EventRecommendationService $service)
    {
    }

    /**
     * Public endpoint to create an Event Recommendation.
     * - Available to guests and authenticated users
     * - Always sets status to 'pending' to avoid privilege abuse
     */
    public function store(Request $request)
    {
        try {
            $rules = EventRecommendationRules::store();
            // Force safe defaults for public submissions
            $request->merge([
                'status' => 'pending',
            ]);

            $validated = $request->validate($rules);

            $created = $this->service->create($validated);

            return response()->json([
                'message' => 'Recommendation submitted successfully',
                'data' => [
                    'id' => $created->id,
                ],
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('API V1 EventRecommendation store error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'An unexpected error occurred',
            ], 500);
        }
    }
}

