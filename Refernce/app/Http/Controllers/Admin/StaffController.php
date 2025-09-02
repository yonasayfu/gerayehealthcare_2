<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreateStaffDTO;
use App\DTOs\UpdateStaffDTO;
use App\Exports\StaffExport;
use App\Http\Controllers\OptimizedBaseController;
use App\Models\Staff;
use App\Services\StaffService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;

class StaffController extends OptimizedBaseController
{
    protected StaffService $staffService;

    public function __construct(StaffService $staffService)
    {
        $this->staffService = $staffService;

        // Apply middleware for permissions
        $this->middleware('can:view-staff')->only(['index', 'show']);
        $this->middleware('can:create-staff')->only(['create', 'store']);
        $this->middleware('can:edit-staff')->only(['edit', 'update']);
        $this->middleware('can:delete-staff')->only(['destroy']);
    }

    /**
     * Display a listing of staff members
     */
    public function index(Request $request): Response
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

            $statistics = $this->staffService->getStatistics();
            $departments = $this->staffService->getDepartments();
            $positions = $this->staffService->getPositions();

            return Inertia::render('Admin/Staff/Index', [
                'staff' => $staff,
                'statistics' => $statistics,
                'departments' => $departments,
                'positions' => $positions,
                'filters' => $request->only([
                    'search', 'department', 'status', 'employment_type',
                    'sort_by', 'sort_order', 'per_page',
                ]),
                'employmentTypes' => Staff::EMPLOYMENT_TYPES,
                'statusTypes' => Staff::STATUS_TYPES,
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to retrieve staff members');
        }
    }

    /**
     * Show the form for creating a new staff member
     */
    public function create(): Response
    {
        $departments = $this->staffService->getDepartments();
        $positions = $this->staffService->getPositions();

        return Inertia::render('Admin/Staff/Create', [
            'departments' => $departments,
            'positions' => $positions,
            'employmentTypes' => Staff::EMPLOYMENT_TYPES,
            'statusTypes' => Staff::STATUS_TYPES,
        ]);
    }

    /**
     * Store a newly created staff member
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            // Validate the request
            $validated = $request->validate((new CreateStaffDTO)->rules(), (new CreateStaffDTO)->messages());

            $dto = CreateStaffDTO::fromRequest($request);
            $staff = $this->staffService->create($dto);

            return redirect()
                ->route('admin.staff.index')
                ->with('success', 'Staff member created successfully');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to create staff member');
        }
    }

    /**
     * Display the specified staff member
     */
    public function show(int $id): Response
    {
        try {
            $staff = $this->staffService->getById($id);
            $staff->load(['user']);

            return Inertia::render('Admin/Staff/Show', [
                'staff' => $staff,
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Staff member not found');
        }
    }

    /**
     * Show the form for editing the specified staff member
     */
    public function edit(int $id): Response
    {
        try {
            $staff = $this->staffService->getById($id);
            $staff->load(['user']);

            $departments = $this->staffService->getDepartments();
            $positions = $this->staffService->getPositions();

            return Inertia::render('Admin/Staff/Edit', [
                'staff' => $staff,
                'departments' => $departments,
                'positions' => $positions,
                'employmentTypes' => Staff::EMPLOYMENT_TYPES,
                'statusTypes' => Staff::STATUS_TYPES,
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Staff member not found');
        }
    }

    /**
     * Update the specified staff member
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        try {
            // Validate the request
            $dto = new UpdateStaffDTO;
            $dto->setStaffId($id);
            $validated = $request->validate($dto->rules(), $dto->messages());

            $dto = UpdateStaffDTO::fromRequest($request, $id);
            $staff = $this->staffService->update($id, $dto);

            return redirect()
                ->route('admin.staff.show', $staff->id)
                ->with('success', 'Staff member updated successfully');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to update staff member');
        }
    }

    /**
     * Remove the specified staff member
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->staffService->delete($id);

            return redirect()
                ->route('admin.staff.index')
                ->with('success', 'Staff member deleted successfully');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to delete staff member');
        }
    }

    /**
     * Export staff data to CSV
     */
    public function export(Request $request)
    {
        try {
            $filters = $request->only(['search', 'department', 'status', 'employment_type']);
            $staff = $this->staffService->getForExport($filters);

            $filename = 'staff_export_'.now()->format('Y-m-d_H-i-s').'.csv';

            return Excel::download(new StaffExport($staff), $filename);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to export staff data');
        }
    }

    /**
     * Print staff list
     */
    public function print(Request $request): Response
    {
        try {
            $filters = $request->only(['search', 'department', 'status', 'employment_type']);
            $staff = $this->staffService->getForExport($filters);
            $statistics = $this->staffService->getStatistics();

            return Inertia::render('Admin/Staff/Print', [
                'staff' => $staff,
                'statistics' => $statistics,
                'filters' => $filters,
                'printDate' => now()->format('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to generate print view');
        }
    }

    /**
     * Upload profile photo
     */
    public function uploadPhoto(Request $request, int $id)
    {
        try {
            $request->validate([
                'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $staff = $this->staffService->getById($id);

            // Delete old photo if exists
            if ($staff->profile_photo_path) {
                Storage::disk('public')->delete($staff->profile_photo_path);
            }

            // Store new photo
            $filename = 'staff_'.$staff->id.'_'.time().'.'.$request->file('photo')->getClientOriginalExtension();
            $path = $request->file('photo')->storeAs('staff/photos', $filename, 'public');

            // Update staff record
            $dto = UpdateStaffDTO::fromRequest(new Request(['profile_photo_path' => $path]), $id);
            $this->staffService->update($id, $dto);

            if ($request->wantsJson()) {
                return $this->success([
                    'photo_url' => asset('storage/'.$path),
                ], 'Photo uploaded successfully');
            }

            return redirect()->back()->with('success', 'Photo uploaded successfully');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to upload photo');
        }
    }
}
