<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreateStaffDTO;
use App\Http\Controllers\Base\BaseController;
use App\Models\Staff;
use App\Services\Staff\StaffService;
use App\Services\Validation\Rules\StaffRules;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StaffController extends BaseController
{
    public function __construct(StaffService $staffService)
    {
        parent::__construct(
            $staffService,
            StaffRules::class,
            'Admin/Staff',
            'staff',
            Staff::class,
            CreateStaffDTO::class
        );
    }

    public function create()
    {
        return Inertia::render('Admin/Staff/Create', [
            'departments' => config('hr.departments'),
            'positions' => config('hr.positions'),
        ]);
    }

    public function export(Request $request)
    {
        return app(StaffService::class)->export($request);
    }

    public function edit($id)
    {
        $staff = $this->service->getById($id);

        return Inertia::render('Admin/Staff/Edit', [
            'staff' => $staff,
            'departments' => config('hr.departments'),
            'positions' => config('hr.positions'),
        ]);
    }

    public function printAll()
    {
        return app(StaffService::class)->printAll(request());
    }

    public function printCurrent()
    {
        return app(StaffService::class)->printCurrent(request());
    }

    public function printSingle(Staff $staff)
    {
        return app(StaffService::class)->printSingle($staff, request());
    }

    public function update(Request $request, $id)
    {
        $staff = $this->service->getById($id);
        $validatedData = $this->validateRequest($request, 'update', $staff);

        $this->service->update($id, $validatedData);

        return redirect()->route('admin.staff.index')->with('banner', 'Staff updated successfully.')->with('bannerStyle', 'success');
    }

    public function destroy(Request $request, $id)
    {
        try {
            // Check if staff member has related records
            $staff = Staff::withCount(['medicalDocuments', 'referralDocuments'])->findOrFail($id);

            // If there are related records, we can't delete
            if ($staff->medical_documents_count > 0 || $staff->referral_documents_count > 0) {
                $message = "Cannot delete staff member. This staff member has ";
                if ($staff->medical_documents_count > 0) {
                    $message .= "{$staff->medical_documents_count} medical documents";
                }
                if ($staff->referral_documents_count > 0) {
                    if ($staff->medical_documents_count > 0) {
                        $message .= " and ";
                    }
                    $message .= "{$staff->referral_documents_count} referral documents";
                }
                $message .= " associated with them. Please reassign these documents first.";

                return back()->with('banner', $message)->with('bannerStyle', 'danger');
            }

            // If no related records, proceed with deletion
            $this->service->delete($id);

            return redirect()->route('admin.staff.index')->with('banner', 'Staff member deleted successfully.')->with('bannerStyle', 'success');
        } catch (\Exception $e) {
            return back()->with('banner', 'An unexpected error occurred during deletion: ' . $e->getMessage())->with('bannerStyle', 'danger');
        }
    }
}
