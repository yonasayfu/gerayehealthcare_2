<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\StaffPayoutService;
use App\Services\Validation\Rules\StaffPayoutRules;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Carbon;

class StaffPayoutController extends Controller
{
    protected $staffPayoutService;

    public function __construct(StaffPayoutService $staffPayoutService)
    {
        $this->staffPayoutService = $staffPayoutService;
    }

    public function index()
    {
        $data = $this->staffPayoutService->getStaffEarningsData();
        return Inertia::render('Admin/StaffPayouts/Index', $data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate(StaffPayoutRules::store());

        try {
            $dto = new CreateStaffPayoutDTO(
                staff_id: $validated['staff_id'],
                total_amount: 0, // This will be calculated in the service
                payout_date: Carbon::today()->toDateString(),
                status: 'Completed',
                notes: 'Manual Payout'
            );
            $this->staffPayoutService->processPayout($dto);
            return back()->with('success', 'Payout processed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
