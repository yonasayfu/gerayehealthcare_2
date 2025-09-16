<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreateStaffDTO;
use App\Http\Controllers\Base\OptimizedBaseController;
use App\Models\Staff;
use App\Services\Optimized\Staff\StaffService;
use App\Services\Validation\Rules\StaffRules;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OptimizedStaffController extends OptimizedBaseController
{
    protected $staffService;

    // Define eager loading relationships to prevent N+1 queries
    protected $indexWith = ['user'];

    protected $showWith = ['user'];

    protected $editWith = ['user'];

    public function __construct(OptimizedStaffService $staffService)
    {
        $this->staffService = $staffService;

        parent::__construct(
            $staffService,
            StaffRules::class,
            'Admin/Staff',
            'staff',
            Staff::class,
            CreateStaffDTO::class
        );
    }

    public function index(Request $request)
    {
        // Use optimized service with caching and eager loading
        $staff = $this->staffService->getAll($request, $this->indexWith);

        // Get cached statistics for dashboard info
        $statistics = $this->staffService->getStatistics();

        return Inertia::render($this->viewName.'/Index', [
            'staff' => $staff,
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page']),
            'statistics' => $statistics, // Additional dashboard data
        ]);
    }

    public function show($id)
    {
        // Use optimized service with eager loading
        $staff = $this->staffService->getById($id, $this->showWith);

        return Inertia::render($this->viewName.'/Show', [
            'staff' => $staff,
        ]);
    }

    public function create()
    {
        // Use cached form data
        $formData = $this->staffService->getFormData();

        return Inertia::render($this->viewName.'/Create', $formData);
    }

    public function edit($id)
    {
        // Use optimized service with eager loading
        $staff = $this->staffService->getById($id, $this->editWith);
        $formData = $this->staffService->getFormData();

        return Inertia::render($this->viewName.'/Edit', array_merge([
            'staff' => $staff,
        ], $formData));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate(StaffRules::create());

        // Use DTO for data transfer
        $dto = CreateStaffDTO::from($validatedData);
        $staff = $this->staffService->create($dto);

        return redirect()->route('admin.staff.index')
            ->with('banner', 'Staff member created successfully!')
            ->with('bannerStyle', 'success');
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate(StaffRules::update());

        // Use DTO for data transfer
        $dto = CreateStaffDTO::from($validatedData);
        $staff = $this->staffService->update($id, $dto);

        return redirect()->route('admin.staff.index')
            ->with('banner', 'Staff member updated successfully!')
            ->with('bannerStyle', 'success');
    }

    public function destroy($id)
    {
        $this->staffService->delete($id);

        return redirect()->route('admin.staff.index')
            ->with('banner', 'Staff member deleted successfully!')
            ->with('bannerStyle', 'success');
    }

    // Optimized export methods
    public function export(Request $request)
    {
        return $this->staffService->export($request);
    }

    public function printAll(Request $request)
    {
        return $this->staffService->printAll($request);
    }

    public function printCurrent(Request $request)
    {
        return $this->staffService->printCurrent($request);
    }

    public function printSingle($id)
    {
        return $this->staffService->printSingle($id, request());
    }

    // API endpoint for staff availability lookup (highly optimized)
    public function availableStaff(Request $request)
    {
        $date = $request->input('date');
        $timeSlot = $request->input('time_slot');

        $availableStaff = $this->staffService->getAvailableStaff($date, $timeSlot);

        return response()->json($availableStaff);
    }

    // API endpoint for quick staff search (optimized)
    public function quickSearch(Request $request)
    {
        $search = $request->input('q');

        if (strlen($search) < 2) {
            return response()->json([]);
        }

        // Cache quick searches for better performance
        $cacheKey = 'staff_quick_search_'.md5($search);

        $results = \Illuminate\Support\Facades\Cache::remember($cacheKey, 300, function () use ($search) {
            return Staff::where('first_name', 'ilike', "%{$search}%")
                ->orWhere('last_name', 'ilike', "%{$search}%")
                ->orWhere('email', 'ilike', "%{$search}%")
                ->select('id', 'first_name', 'last_name', 'position', 'department')
                ->limit(10)
                ->get();
        });

        return response()->json($results);
    }

    // Bulk operations for staff management
    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'staff_ids' => 'required|array',
            'staff_ids.*' => 'exists:staff,id',
            'action' => 'required|in:activate,deactivate,update_department',
            'value' => 'nullable|string',
        ]);

        $updatedCount = 0;
        foreach ($validated['staff_ids'] as $staffId) {
            switch ($validated['action']) {
                case 'activate':
                    $this->staffService->update($staffId, ['status' => 'active']);
                    $updatedCount++;
                    break;
                case 'deactivate':
                    $this->staffService->update($staffId, ['status' => 'inactive']);
                    $updatedCount++;
                    break;
                case 'update_department':
                    if ($validated['value']) {
                        $this->staffService->update($staffId, ['department' => $validated['value']]);
                        $updatedCount++;
                    }
                    break;
            }
        }

        return response()->json([
            'message' => "Updated {$updatedCount} staff members successfully",
            'updated_count' => $updatedCount,
        ]);
    }
}
