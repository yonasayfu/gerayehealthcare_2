<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\InventoryItem;
use App\Models\Patient;
use App\Models\Staff;
use App\Models\User;
use App\Models\VisitService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class BulkOperationsController extends BaseApiController
{
    /**
     * Bulk create patients
     */
    public function createPatients(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'patients' => 'required|array|min:1|max:100',
                'patients.*.full_name' => 'required|string|max:255',
                'patients.*.email' => 'nullable|email|max:255',
                'patients.*.phone_number' => 'nullable|string|max:20',
                'patients.*.date_of_birth' => 'nullable|date',
                'patients.*.gender' => 'nullable|in:male,female,other',
                'patients.*.address' => 'nullable|string',
                'patients.*.city' => 'nullable|string|max:100',
                'patients.*.emergency_contact_name' => 'nullable|string|max:255',
                'patients.*.emergency_contact_phone' => 'nullable|string|max:20',
                'patients.*.medical_history' => 'nullable|string',
                'patients.*.allergies' => 'nullable|string',
                'patients.*.current_medications' => 'nullable|string',
                'patients.*.lead_source' => 'nullable|string|max:100',
                'patients.*.marketing_campaign_id' => 'nullable|exists:marketing_campaigns,id',
                'patients.*.notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Validation failed', 422, $validator->errors());
            }

            $patients = [];
            $errors = [];
            $successCount = 0;

            DB::beginTransaction();

            foreach ($request->patients as $index => $patientData) {
                try {
                    // Check for duplicate email or phone
                    if (! empty($patientData['email'])) {
                        $existingPatient = Patient::where('email', $patientData['email'])->first();
                        if ($existingPatient) {
                            $errors[] = "Row {$index}: Email already exists for patient: {$existingPatient->full_name}";

                            continue;
                        }
                    }

                    $patient = Patient::create(array_merge($patientData, [
                        'status' => 'active',
                        'created_by' => Auth::id(),
                    ]));

                    $patients[] = $patient;
                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "Row {$index}: ".$e->getMessage();
                }
            }

            DB::commit();

            return $this->successResponse([
                'created_patients' => $patients,
                'success_count' => $successCount,
                'error_count' => count($errors),
                'errors' => $errors,
                'message' => "Successfully created {$successCount} patients",
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse('Failed to create patients in bulk', 500);
        }
    }

    /**
     * Bulk update patients
     */
    public function updatePatients(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'updates' => 'required|array|min:1|max:100',
                'updates.*.id' => 'required|exists:patients,id',
                'updates.*.full_name' => 'sometimes|required|string|max:255',
                'updates.*.email' => 'sometimes|nullable|email|max:255',
                'updates.*.phone_number' => 'sometimes|nullable|string|max:20',
                'updates.*.status' => 'sometimes|required|in:active,inactive,deceased',
                'updates.*.notes' => 'sometimes|nullable|string',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Validation failed', 422, $validator->errors());
            }

            $updatedPatients = [];
            $errors = [];
            $successCount = 0;

            DB::beginTransaction();

            foreach ($request->updates as $index => $updateData) {
                try {
                    $patient = Patient::findOrFail($updateData['id']);

                    // Remove id from update data
                    unset($updateData['id']);

                    $patient->update($updateData);
                    $updatedPatients[] = $patient;
                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "Row {$index}: ".$e->getMessage();
                }
            }

            DB::commit();

            return $this->successResponse([
                'updated_patients' => $updatedPatients,
                'success_count' => $successCount,
                'error_count' => count($errors),
                'errors' => $errors,
                'message' => "Successfully updated {$successCount} patients",
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse('Failed to update patients in bulk', 500);
        }
    }

    /**
     * Bulk create staff
     */
    public function createStaff(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'staff' => 'required|array|min:1|max:50',
                'staff.*.name' => 'required|string|max:255',
                'staff.*.email' => 'required|email|max:255|unique:users,email',
                'staff.*.phone' => 'nullable|string|max:20',
                'staff.*.specialization' => 'required|string|max:255',
                'staff.*.qualification' => 'nullable|string',
                'staff.*.experience_years' => 'nullable|integer|min:0',
                'staff.*.hourly_rate' => 'nullable|numeric|min:0',
                'staff.*.role' => 'required|string|in:admin,manager,caregiver,nurse,doctor',
                'staff.*.status' => 'required|in:active,inactive',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Validation failed', 422, $validator->errors());
            }

            $createdStaff = [];
            $errors = [];
            $successCount = 0;

            DB::beginTransaction();

            foreach ($request->staff as $index => $staffData) {
                try {
                    // Create user first
                    $user = User::create([
                        'name' => $staffData['name'],
                        'email' => $staffData['email'],
                        'password' => Hash::make('password123'), // Default password
                        'email_verified_at' => now(),
                    ]);

                    // Assign role
                    $user->assignRole($staffData['role']);

                    // Create staff record
                    $staff = Staff::create([
                        'user_id' => $user->id,
                        'phone' => $staffData['phone'] ?? null,
                        'specialization' => $staffData['specialization'],
                        'qualification' => $staffData['qualification'] ?? null,
                        'experience_years' => $staffData['experience_years'] ?? null,
                        'hourly_rate' => $staffData['hourly_rate'] ?? null,
                        'status' => $staffData['status'],
                        'availability_status' => 'available',
                    ]);

                    $createdStaff[] = $staff->load('user');
                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "Row {$index}: ".$e->getMessage();
                }
            }

            DB::commit();

            return $this->successResponse([
                'created_staff' => $createdStaff,
                'success_count' => $successCount,
                'error_count' => count($errors),
                'errors' => $errors,
                'message' => "Successfully created {$successCount} staff members. Default password: password123",
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse('Failed to create staff in bulk', 500);
        }
    }

    /**
     * Bulk create inventory items
     */
    public function createInventoryItems(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'items' => 'required|array|min:1|max:100',
                'items.*.name' => 'required|string|max:255',
                'items.*.sku' => 'required|string|max:100|unique:inventory_items,sku',
                'items.*.category' => 'required|string|max:100',
                'items.*.unit_of_measure' => 'required|string|max:50',
                'items.*.unit_cost' => 'required|numeric|min:0',
                'items.*.selling_price' => 'nullable|numeric|min:0',
                'items.*.current_stock' => 'required|integer|min:0',
                'items.*.reorder_level' => 'required|integer|min:0',
                'items.*.supplier_id' => 'nullable|exists:suppliers,id',
                'items.*.location' => 'nullable|string|max:255',
                'items.*.status' => 'required|in:active,inactive,discontinued',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Validation failed', 422, $validator->errors());
            }

            $createdItems = [];
            $errors = [];
            $successCount = 0;

            DB::beginTransaction();

            foreach ($request->items as $index => $itemData) {
                try {
                    $item = InventoryItem::create($itemData);
                    $createdItems[] = $item;
                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "Row {$index}: ".$e->getMessage();
                }
            }

            DB::commit();

            return $this->successResponse([
                'created_items' => $createdItems,
                'success_count' => $successCount,
                'error_count' => count($errors),
                'errors' => $errors,
                'message' => "Successfully created {$successCount} inventory items",
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse('Failed to create inventory items in bulk', 500);
        }
    }

    /**
     * Bulk delete records
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'model' => 'required|string|in:patients,staff,inventory_items,visit_services',
                'ids' => 'required|array|min:1|max:100',
                'ids.*' => 'required|integer',
                'force' => 'boolean',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Validation failed', 422, $validator->errors());
            }

            $model = $this->getModelClass($request->model);
            $force = $request->boolean('force', false);

            $deletedCount = 0;
            $errors = [];

            DB::beginTransaction();

            foreach ($request->ids as $id) {
                try {
                    $record = $model::findOrFail($id);

                    // Check for dependencies before deletion
                    if (! $force && $this->hasDependencies($record, $request->model)) {
                        $errors[] = "ID {$id}: Cannot delete due to existing dependencies";

                        continue;
                    }

                    $record->delete();
                    $deletedCount++;
                } catch (\Exception $e) {
                    $errors[] = "ID {$id}: ".$e->getMessage();
                }
            }

            DB::commit();

            return $this->successResponse([
                'deleted_count' => $deletedCount,
                'error_count' => count($errors),
                'errors' => $errors,
                'message' => "Successfully deleted {$deletedCount} records",
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse('Failed to delete records in bulk', 500);
        }
    }

    /**
     * Bulk export data
     */
    public function bulkExport(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'model' => 'required|string|in:patients,staff,visit_services,inventory_items',
                'format' => 'required|string|in:csv,json,excel',
                'filters' => 'nullable|array',
                'columns' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Validation failed', 422, $validator->errors());
            }

            $model = $this->getModelClass($request->model);
            $query = $model::query();

            // Apply filters if provided
            if ($request->has('filters')) {
                $this->applyFilters($query, $request->filters);
            }

            // Select specific columns if provided
            if ($request->has('columns')) {
                $query->select($request->columns);
            }

            $data = $query->get();

            return $this->successResponse([
                'data' => $data,
                'count' => $data->count(),
                'format' => $request->format,
                'exported_at' => now()->toISOString(),
                'message' => "Successfully exported {$data->count()} records",
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to export data', 500);
        }
    }

    /**
     * Get bulk operation status
     */
    public function getOperationStatus(Request $request, string $operationId): JsonResponse
    {
        try {
            // This would typically check a job queue or cache for operation status
            // For now, return a simple response
            return $this->successResponse([
                'operation_id' => $operationId,
                'status' => 'completed',
                'progress' => 100,
                'message' => 'Operation completed successfully',
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get operation status', 500);
        }
    }

    // Helper methods
    private function getModelClass(string $modelName): string
    {
        $models = [
            'patients' => Patient::class,
            'staff' => Staff::class,
            'inventory_items' => InventoryItem::class,
            'visit_services' => VisitService::class,
        ];

        return $models[$modelName];
    }

    private function hasDependencies($record, string $modelName): bool
    {
        switch ($modelName) {
            case 'patients':
                return $record->visitServices()->exists() || $record->insurancePolicies()->exists();
            case 'staff':
                return $record->visitServices()->exists();
            case 'inventory_items':
                return $record->current_stock > 0 || $record->transactions()->exists();
            default:
                return false;
        }
    }

    private function applyFilters($query, array $filters): void
    {
        foreach ($filters as $field => $value) {
            if (is_array($value)) {
                $query->whereIn($field, $value);
            } else {
                $query->where($field, $value);
            }
        }
    }
}
