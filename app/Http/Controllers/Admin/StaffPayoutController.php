<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\StaffPayoutService;
use App\Services\Validation\Rules\StaffPayoutRules;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
            $this->staffPayoutService->processPayout($validated['staff_id']);
            return back()->with('success', 'Payout processed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
