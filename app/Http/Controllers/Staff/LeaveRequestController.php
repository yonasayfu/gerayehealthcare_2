<?php

namespace App\Http\Controllers\Staff;

use App\DTOs\CreateLeaveRequestDTO;
use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStaffLeaveRequest;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Notifications\LeaveRequestSubmitted;
use App\Services\LeaveRequest\LeaveRequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LeaveRequestController extends Controller
{
    protected $leaveRequestService;

    public function __construct(LeaveRequestService $leaveRequestService)
    {
        $this->leaveRequestService = $leaveRequestService;
    }

    /**
     * Display a listing of the authenticated staff's leave requests.
     */
    public function index()
    {
        $user = Auth::user();
        $staff = $user->staff;

        // Bypass staff profile requirement for admin roles
        $isAdmin = $user->hasAnyRole([RoleEnum::SUPER_ADMIN->value, RoleEnum::CEO->value, RoleEnum::COO->value, RoleEnum::ADMIN->value]);

        if (!$staff && !$isAdmin) {
            return redirect()
                ->route('dashboard')
                ->with('banner', 'Your account is not linked to a staff profile. Please contact an administrator to set up your staff profile to request leave.')
                ->with('bannerStyle', 'danger');
        }
        // For admin users without staff profile, show all leave requests
        // For regular staff, show only their own requests
        if ($isAdmin && !$staff) {
            $leaveRequests = LeaveRequest::with('staff')
                ->latest()
                ->paginate(10);
        } else {
            $leaveRequests = LeaveRequest::where('staff_id', $staff->id)
                ->latest()
                ->paginate(10);
        }

        // For admin users without staff profile, don't calculate leave policy stats
        if ($isAdmin && !$staff) {
            $leavePolicy = null;
        } else {
            // Simple leave policy: 20 days per calendar year
            $policyDaysPerYear = 20;
            $startOfYear = Carbon::now()->copy()->startOfYear();
            $endOfYear = Carbon::now()->copy()->endOfYear();

            $approvedLeaves = $staff->leaveRequests()
                ->where('status', 'Approved')
                ->where(function ($q) use ($startOfYear, $endOfYear) {
                    $q->whereBetween('start_date', [$startOfYear, $endOfYear])
                        ->orWhereBetween('end_date', [$startOfYear, $endOfYear])
                        ->orWhere(function ($qq) use ($startOfYear, $endOfYear) {
                            $qq->where('start_date', '<=', $startOfYear)
                                ->where('end_date', '>=', $endOfYear);
                        });
                })
                ->get(['start_date', 'end_date']);

            $usedDays = $approvedLeaves->reduce(function ($carry, $lr) use ($startOfYear, $endOfYear) {
                $from = Carbon::parse($lr->start_date)->max($startOfYear);
                $to = Carbon::parse($lr->end_date)->min($endOfYear);
                return $carry + max(0, $to->diffInDays($from) + 1);
            }, 0);

            $pendingDays = $staff->leaveRequests()
                ->where('status', 'Pending')
                ->get(['start_date', 'end_date'])
                ->reduce(function ($carry, $lr) {
                    $from = Carbon::parse($lr->start_date);
                    $to = Carbon::parse($lr->end_date);
                    return $carry + max(0, $to->diffInDays($from) + 1);
                }, 0);

            $leavePolicy = [
                'annual_days' => $policyDaysPerYear,
                'used_days' => $usedDays,
                'remaining_days' => max(0, $policyDaysPerYear - $usedDays),
                'pending_days' => $pendingDays,
                'year' => $startOfYear->year,
            ];
        }

        return Inertia::render('Staff/MyLeaveRequests/Index', [
            'leaveRequests' => $leaveRequests,
            'leavePolicy' => $leavePolicy,
            'isAdmin' => $isAdmin,
        ]);
    }

    /**
     * Store a newly created leave request in storage.
     */
    public function store(StoreStaffLeaveRequest $request)
    {
        $user = Auth::user();
        $staff = $user->staff;

        // Bypass staff profile requirement for admin roles
        $isAdmin = $user->hasAnyRole([RoleEnum::SUPER_ADMIN->value, RoleEnum::CEO->value, RoleEnum::COO->value, RoleEnum::ADMIN->value]);

        if (!$staff && !$isAdmin) {
            return redirect()
                ->back()
                ->with('banner', 'Cannot submit leave request: no staff profile linked to your account.')
                ->with('bannerStyle', 'danger');
        }

        // Allow admin users to submit leave requests for themselves even without staff profiles
        // In this case, we'll create a staff profile for them automatically
        if ($isAdmin && !$staff) {
            // Create a staff profile for the admin user
            $staff = $user->staff()->create([
                'first_name' => $user->name,
                'last_name' => '',
                'email' => $user->email,
                'phone' => '',
                'position' => 'Administrator',
                'department' => 'Administration',
                'hire_date' => now(),
                'is_active' => true,
            ]);
        }

        $validated = $request->validated();
        $dto = new CreateLeaveRequestDTO(
            staff_id: $staff->id,
            start_date: $validated['start_date'],
            end_date: $validated['end_date'],
            reason: $validated['reason'],
            status: 'Pending',
            admin_notes: null
        );

        $created = $this->leaveRequestService->create($dto);

        // Notify approvers (Super Admin, Admin, CEO, COO)
        $recipients = User::role([
            RoleEnum::SUPER_ADMIN->value,
            RoleEnum::ADMIN->value,
            RoleEnum::CEO->value,
            RoleEnum::COO->value,
        ])->get();
        foreach ($recipients as $user) {
            $user->notify(new LeaveRequestSubmitted($created));
        }

        return redirect()->back()->with('banner', 'Leave request submitted successfully.')->with('bannerStyle', 'success');
    }
}
