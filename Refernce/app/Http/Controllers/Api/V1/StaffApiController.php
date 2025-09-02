<?php

namespace App\Http\Controllers\Api\V1;

use App\DTOs\CreateStaffDTO;
use App\DTOs\UpdateStaffDTO;
use App\Models\Staff;
use App\Services\StaffService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StaffApiController extends BaseApiController
{
    protected StaffService $staffService;

    public function __construct(StaffService $staffService)
    {
        $this->staffService = $staffService;

        // Apply middleware for API authentication and permissions
        $this->middleware('auth:sanctum');
        $this->middleware('can:view-staff')->only(['index', 'show', 'dropdown', 'search', 'stats']);
        $this->middleware('can:create-staff')->only(['store']);
        $this->middleware('can:edit-staff')->only(['update', 'uploadPhoto']);
        $this->middleware('can:delete-staff')->only(['destroy']);
    }

    /**
     * Display a listing of staff members
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $search = $request->get('search');
            $department = $request->get('department');
            $status = $request->get('status');
            $employmentType = $request->get('employment_type');
            $sortBy = $request->get('sort_by', 'first_name');
            $sortOrder = $request->get('sort_order', 'asc');

            $staff = $this->staffService->getPaginated(
                $perPage,
                $search,
                $department,
                $status,
                $employmentType,
                $sortBy,
                $sortOrder
            );

            return $this->success($staff, 'Staff members retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve staff members', 500, $e->getMessage());
        }
    }

    /**
     * Store a newly created staff member
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validate the request
            $validated = $request->validate((new CreateStaffDTO)->rules(), (new CreateStaffDTO)->messages());

            $dto = CreateStaffDTO::fromRequest($request);
            $staff = $this->staffService->create($dto);

            return $this->success($staff->load('user'), 'Staff member created successfully', 201);
        } catch (\Exception $e) {
            return $this->error('Failed to create staff member', 422, $e->getMessage());
        }
    }

    /**
     * Display the specified staff member
     */
    public function show(int $id): JsonResponse
    {
        try {
            $staff = $this->staffService->getById($id);
            $staff->load(['user']);

            return $this->success($staff, 'Staff member retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Staff member not found', 404, $e->getMessage());
        }
    }

    /**
     * Update the specified staff member
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            // Validate the request
            $dto = new UpdateStaffDTO;
            $dto->setStaffId($id);
            $validated = $request->validate($dto->rules(), $dto->messages());

            $dto = UpdateStaffDTO::fromRequest($request, $id);
            $staff = $this->staffService->update($id, $dto);

            return $this->success($staff->load('user'), 'Staff member updated successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to update staff member', 422, $e->getMessage());
        }
    }

    /**
     * Remove the specified staff member
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->staffService->delete($id);

            return $this->success(null, 'Staff member deleted successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to delete staff member', 422, $e->getMessage());
        }
    }

    /**
     * Get staff dropdown data
     */
    public function dropdown(Request $request): JsonResponse
    {
        try {
            $includeInactive = $request->boolean('include_inactive', false);
            $department = $request->get('department');

            $query = Staff::select('id', 'first_name', 'last_name', 'position', 'department')
                ->selectRaw("CONCAT(first_name, ' ', last_name) as full_name");

            if (! $includeInactive) {
                $query->active();
            }

            if ($department) {
                $query->byDepartment($department);
            }

            $staff = $query->orderBy('first_name')->get();

            return $this->success($staff, 'Staff dropdown data retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve staff dropdown data', 500, $e->getMessage());
        }
    }

    /**
     * Search staff members
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $search = $request->get('q', '');
            $limit = $request->get('limit', 10);

            if (empty($search)) {
                return $this->success([], 'Search query is required');
            }

            $staff = Staff::search($search)
                ->select('id', 'first_name', 'last_name', 'position', 'department', 'email')
                ->selectRaw("CONCAT(first_name, ' ', last_name) as full_name")
                ->active()
                ->limit($limit)
                ->get();

            return $this->success($staff, 'Search results retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to search staff members', 500, $e->getMessage());
        }
    }

    /**
     * Get staff statistics
     */
    public function stats(): JsonResponse
    {
        try {
            $statistics = $this->staffService->getStatistics();

            return $this->success($statistics, 'Staff statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve staff statistics', 500, $e->getMessage());
        }
    }

    /**
     * Get available staff (active and not on leave)
     */
    public function available(): JsonResponse
    {
        try {
            $staff = Staff::active()
                ->where('status', 'active')
                ->select('id', 'first_name', 'last_name', 'position', 'department')
                ->selectRaw("CONCAT(first_name, ' ', last_name) as full_name")
                ->orderBy('first_name')
                ->get();

            return $this->success($staff, 'Available staff retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve available staff', 500, $e->getMessage());
        }
    }

    /**
     * Upload profile photo
     */
    public function uploadPhoto(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $staff = $this->staffService->getById($id);

            // Delete old photo if exists
            if ($staff->profile_photo_path) {
                \Storage::disk('public')->delete($staff->profile_photo_path);
            }

            // Store new photo
            $filename = 'staff_'.$staff->id.'_'.time().'.'.$request->file('photo')->getClientOriginalExtension();
            $path = $request->file('photo')->storeAs('staff/photos', $filename, 'public');

            // Update staff record
            $dto = UpdateStaffDTO::fromRequest(new Request(['profile_photo_path' => $path]), $id);
            $updatedStaff = $this->staffService->update($id, $dto);

            return $this->success([
                'staff' => $updatedStaff,
                'photo_url' => asset('storage/'.$path),
            ], 'Photo uploaded successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to upload photo', 422, $e->getMessage());
        }
    }

    /**
     * Get departments list
     */
    public function departments(): JsonResponse
    {
        try {
            $departments = $this->staffService->getDepartments();

            return $this->success($departments, 'Departments retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve departments', 500, $e->getMessage());
        }
    }

    /**
     * Get positions list
     */
    public function positions(): JsonResponse
    {
        try {
            $positions = $this->staffService->getPositions();

            return $this->success($positions, 'Positions retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve positions', 500, $e->getMessage());
        }
    }
}
