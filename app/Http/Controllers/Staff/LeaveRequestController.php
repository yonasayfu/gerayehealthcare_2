<?php

namespace App\Http\Controllers\Staff;

use App\DTOs\CreateLeaveRequestDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStaffLeaveRequest;
use App\Models\LeaveRequest;
use App\Services\LeaveRequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use App\Enums\RoleEnum;
use App\Models\User;
use App\Notifications\LeaveRequestSubmitted;

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
        $staff = Auth::user()->staff;
        if (!$staff) {
            return redirect()
                ->route('dashboard')
                ->with('banner', 'Your account is not linked to a staff profile. Please contact an administrator to set up your staff profile to request leave.')
                ->with('bannerStyle', 'danger');
        }
        $leaveRequests = LeaveRequest::where('staff_id', $staff->id)
            ->latest()
            ->paginate(10);

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

        return Inertia::render('Staff/MyLeaveRequests/Index', [
            'leaveRequests' => $leaveRequests,
            'leavePolicy' => [
                'annual_days' => $policyDaysPerYear,
                'used_days' => $usedDays,
                'remaining_days' => max(0, $policyDaysPerYear - $usedDays),
                'pending_days' => $pendingDays,
                'year' => $startOfYear->year,
            ],
        ]);
    }

    /**
     * Store a newly created leave request in storage.
     */
    public function store(StoreStaffLeaveRequest $request)
    {
        $staff = Auth::user()->staff;
        if (!$staff) {
            return redirect()
                ->back()
                ->with('banner', 'Cannot submit leave request: no staff profile linked to your account.')
                ->with('bannerStyle', 'danger');
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
