<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreateStaffPayoutDTO;
use App\Http\Controllers\Controller;
use App\Models\StaffPayout;
use App\Services\StaffPayoutService;
use App\Services\Validation\Rules\StaffPayoutRules;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class StaffPayoutController extends Controller
{
    protected $staffPayoutService;

    public function __construct(StaffPayoutService $staffPayoutService)
    {
        $this->staffPayoutService = $staffPayoutService;
    }

    public function index(Request $request)
    {
        $data = $this->staffPayoutService->getStaffEarningsData($request);

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

            return back()->with('banner', 'Payout processed successfully.')->with('bannerStyle', 'success');
        } catch (\Exception $e) {
            return back()->with('banner', $e->getMessage())->with('bannerStyle', 'danger');
        }
    }

    public function printAll()
    {
        $payouts = StaffPayout::with('staff')
            ->orderByDesc('payout_date')
            ->limit(500)
            ->get();

        return Inertia::render('Admin/StaffPayouts/PrintAll', [
            'payouts' => $payouts,
        ]);
    }
}
