<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\Staff\StoreStaffRequest;
use App\Http\Requests\Api\V1\Staff\UpdateStaffRequest;
use App\Http\Resources\StaffResource;
use App\Models\Staff;
use App\Services\Staff\StaffService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StaffController extends BaseApiController
{
    public function __construct(private readonly StaffService $staffService)
    {
    }

    public function index(Request $request)
    {
        $staffCollection = $this->staffService->getAll($request);
        $resource = StaffResource::collection($staffCollection)->response()->getData(true);

        return $this->successResponse([
            'staff' => $resource['data'] ?? [],
            'pagination' => [
                'current_page' => $staffCollection->currentPage(),
                'last_page' => $staffCollection->lastPage(),
                'per_page' => $staffCollection->perPage(),
                'total' => $staffCollection->total(),
                'from' => $staffCollection->firstItem(),
                'to' => $staffCollection->lastItem(),
                'has_more' => $staffCollection->hasMorePages(),
            ],
        ]);
    }

    public function show(Staff $staff)
    {
        $payload = (new StaffResource($staff))->response()->getData(true);

        return $this->successResponse([
            'staff' => $payload['data'] ?? null,
        ]);
    }

    public function store(StoreStaffRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo');
        }

        $staff = $this->staffService->create($data)->fresh();
        $payload = (new StaffResource($staff))->response()->getData(true);

        return $this->createdResponse([
            'staff' => $payload['data'] ?? null,
        ], 'Staff member created successfully');
    }

    public function update(UpdateStaffRequest $request, Staff $staff)
    {
        $data = $request->validated();
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo');
        }

        $updated = $this->staffService->update($staff->id, $data)->fresh();
        $payload = (new StaffResource($updated))->response()->getData(true);

        return $this->successResponse([
            'message' => 'Staff member updated successfully',
            'staff' => $payload['data'] ?? null,
        ]);
    }

    public function destroy(Staff $staff)
    {
        $this->staffService->delete($staff->id);

        return $this->successResponse([
            'message' => 'Staff member deleted successfully',
        ], Response::HTTP_OK);
    }
}
