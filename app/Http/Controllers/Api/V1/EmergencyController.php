<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\VisitServiceResource;
use App\Models\VisitService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EmergencyController extends BaseApiController
{
    public function incidents(Request $request): JsonResponse
    {
        $query = VisitService::with(['patient', 'staff'])
            ->where(function ($q) {
                $q->where('priority', 'ILIKE', 'emergency')
                    ->orWhere('service_type', 'ILIKE', 'emergency');
            })
            ->orderByDesc('updated_at');

        if ($request->filled('since')) {
            $query->where('updated_at', '>=', $request->get('since'));
        }

        $incidents = $query->limit(50)->get();

        return $this->successResponse([
            'incidents' => VisitServiceResource::collection($incidents),
        ]);
    }

    public function contacts(): JsonResponse
    {
        $contacts = [
            [
                'name' => 'Emergency Dispatch',
                'phone' => '911',
                'type' => 'external',
                'availability' => '24/7',
            ],
            [
                'name' => 'On-call Doctor',
                'phone' => '+1-555-0101',
                'type' => 'internal',
                'availability' => 'Always Available',
            ],
            [
                'name' => 'Hospital Security',
                'phone' => '+1-555-0199',
                'type' => 'internal',
                'availability' => '24/7',
            ],
        ];

        return $this->successResponse([
            'contacts' => $contacts,
        ]);
    }

    public function alert(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|max:100',
            'location' => 'nullable|string|max:255',
            'description' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        // Placeholder: In a real implementation this would dispatch notifications.
        return $this->successResponse([
            'message' => 'Emergency alert received',
            'submitted_by' => Auth::id(),
            'payload' => $validator->validated(),
        ], 202);
    }
}
