<?php

namespace App\Services;

use App\Models\Staff;
use App\Models\User;
use App\DTOs\CreateStaffDTO;
use App\DTOs\UpdateStaffDTO;
use App\Exceptions\BusinessException;
use App\Exceptions\ServiceException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class StaffService extends PerformanceOptimizedBaseService
{
    protected string $cachePrefix = 'staff';

    public function __construct(Staff $staff)
    {
        parent::__construct($staff);
    }

    /**
     * Get paginated staff with search and filters
     */
    public function getPaginated(
        int $perPage = 15,
        ?string $search = null,
        ?string $department = null,
        ?string $status = null,
        ?string $employmentType = null,
        string $sortBy = 'first_name',
        string $sortOrder = 'asc'
    ): LengthAwarePaginator {
        $cacheKey = "paginated_{$perPage}_{$search}_{$department}_{$status}_{$employmentType}_{$sortBy}_{$sortOrder}";
        
        return $this->getCachedData($cacheKey, function () use (
            $perPage, $search, $department, $status, $employmentType, $sortBy, $sortOrder
        ) {
            $query = $this->model->with(['user']);

            // Apply search
            if ($search) {
                $query->search($search);
            }

            // Apply filters
            if ($department) {
                $query->byDepartment($department);
            }

            if ($status) {
                $query->byStatus($status);
            }

            if ($employmentType) {
                $query->byEmploymentType($employmentType);
            }

            // Apply sorting
            $allowedSortFields = [
                'first_name', 'last_name', 'email', 'position', 
                'department', 'hire_date', 'salary', 'status', 'created_at'
            ];
            
            if (in_array($sortBy, $allowedSortFields)) {
                $query->orderBy($sortBy, $sortOrder);
            } else {
                $query->orderBy('first_name', 'asc');
            }

            return $query->paginate($perPage);
        }, 300); // Cache for 5 minutes
    }

    /**
     * Create new staff member
     */
    public function create(CreateStaffDTO $dto): Staff
    {
        try {
            DB::beginTransaction();

            $data = $dto->transform();
            
            // Handle profile photo upload if present
            if (isset($data['profile_photo']) && $data['profile_photo'] instanceof UploadedFile) {
                $data['profile_photo_path'] = $this->handleProfilePhotoUpload($data['profile_photo']);
                unset($data['profile_photo']);
            }

            $staff = $this->model->create($data);

            // Create associated user if email is provided and no user_id
            if (!$staff->user_id && $staff->email) {
                $user = $this->createAssociatedUser($staff);
                $staff->update(['user_id' => $user->id]);
                $staff->load('user');
            }

            DB::commit();
            
            $this->clearCaches();
            
            return $staff;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new ServiceException('Failed to create staff member: ' . $e->getMessage());
        }
    }

    /**
     * Update staff member
     */
    public function update(int $id, UpdateStaffDTO $dto): Staff
    {
        try {
            DB::beginTransaction();

            $staff = $this->getById($id);
            $data = $dto->transform();

            // Handle profile photo upload if present
            if (isset($data['profile_photo']) && $data['profile_photo'] instanceof UploadedFile) {
                // Delete old photo if exists
                if ($staff->profile_photo_path) {
                    Storage::disk('public')->delete($staff->profile_photo_path);
                }
                
                $data['profile_photo_path'] = $this->handleProfilePhotoUpload($data['profile_photo']);
                unset($data['profile_photo']);
            }

            $staff->update($data);

            // Update associated user email if changed
            if (isset($data['email']) && $staff->user && $staff->user->email !== $data['email']) {
                $staff->user->update(['email' => $data['email']]);
            }

            DB::commit();
            
            $this->clearCaches();
            $this->clearItemCache($id);
            
            return $staff->fresh(['user']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new ServiceException('Failed to update staff member: ' . $e->getMessage());
        }
    }

    /**
     * Delete staff member (soft delete)
     */
    public function delete(int $id): bool
    {
        try {
            $staff = $this->getById($id);
            
            // Check if staff has any dependencies that prevent deletion
            $this->validateDeletion($staff);
            
            $result = $staff->delete();
            
            $this->clearCaches();
            $this->clearItemCache($id);
            
            return $result;
        } catch (\Exception $e) {
            throw new ServiceException('Failed to delete staff member: ' . $e->getMessage());
        }
    }

    /**
     * Get staff statistics
     */
    public function getStatistics(): array
    {
        return $this->getCachedData('statistics', function () {
            return [
                'total_staff' => $this->model->count(),
                'active_staff' => $this->model->active()->count(),
                'by_department' => $this->model->select('department', DB::raw('count(*) as count'))
                    ->groupBy('department')
                    ->pluck('count', 'department')
                    ->toArray(),
                'by_employment_type' => $this->model->select('employment_type', DB::raw('count(*) as count'))
                    ->groupBy('employment_type')
                    ->pluck('count', 'employment_type')
                    ->toArray(),
                'by_status' => $this->model->select('status', DB::raw('count(*) as count'))
                    ->groupBy('status')
                    ->pluck('count', 'status')
                    ->toArray(),
                'recent_hires' => $this->model->where('hire_date', '>=', now()->subDays(30))->count(),
            ];
        }, 600); // Cache for 10 minutes
    }

    /**
     * Get departments list
     */
    public function getDepartments(): array
    {
        return $this->getCachedData('departments', function () {
            return $this->model->distinct()
                ->pluck('department')
                ->filter()
                ->sort()
                ->values()
                ->toArray();
        }, 1800); // Cache for 30 minutes
    }

    /**
     * Get positions list
     */
    public function getPositions(): array
    {
        return $this->getCachedData('positions', function () {
            return $this->model->distinct()
                ->pluck('position')
                ->filter()
                ->sort()
                ->values()
                ->toArray();
        }, 1800); // Cache for 30 minutes
    }

    /**
     * Get staff for export
     */
    public function getForExport(array $filters = []): Collection
    {
        $query = $this->model->with(['user']);

        // Apply filters
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['department'])) {
            $query->byDepartment($filters['department']);
        }

        if (!empty($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        if (!empty($filters['employment_type'])) {
            $query->byEmploymentType($filters['employment_type']);
        }

        return $query->orderBy('first_name')->get();
    }

    /**
     * Handle profile photo upload
     */
    private function handleProfilePhotoUpload(UploadedFile $file): string
    {
        $filename = 'staff_' . uniqid() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('staff/photos', $filename, 'public');
    }

    /**
     * Create associated user account
     */
    private function createAssociatedUser(Staff $staff): User
    {
        $userData = [
            'name' => $staff->full_name,
            'email' => $staff->email,
            'password' => bcrypt('password'), // Default password
            'phone_number' => $staff->phone_number,
            'is_active' => $staff->is_active,
        ];

        $user = User::create($userData);
        
        // Assign staff role
        $user->assignRole('staff');
        
        return $user;
    }

    /**
     * Validate if staff can be deleted
     */
    private function validateDeletion(Staff $staff): void
    {
        // Add any business rules for deletion validation
        // For example, check if staff has pending tasks, is assigned to projects, etc.
        
        // Example validation:
        // if ($staff->hasActiveTasks()) {
        //     throw new BusinessException('Cannot delete staff member with active tasks');
        // }
    }

    /**
     * Clear all staff-related caches
     */
    protected function clearCaches(): void
    {
        parent::clearCaches();
        
        // Clear dropdown caches
        cache()->forget('dropdown_staff_active');
        cache()->forget('dropdown_staff_all');
    }
}
