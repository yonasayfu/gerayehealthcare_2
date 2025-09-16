<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreateStaffPayoutDTO;
use App\Http\Controllers\Controller;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\ExportConfig;
use App\Models\StaffPayout;
use App\Services\StaffPayoutService;
use App\Services\Validation\Rules\StaffPayoutRules;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class StaffPayoutController extends Controller
{
    use ExportableTrait;
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

    public function create()
    {
        $staff = \App\Models\Staff::select('id', 'first_name', 'last_name')->orderBy('first_name')->get();

        return Inertia::render('Admin/StaffPayouts/Create', [
            'staff' => $staff,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate(StaffPayoutRules::store());

        try {
            $dto = new CreateStaffPayoutDTO(
                staff_id: $validated['staff_id'],
                total_amount: null,
                payout_date: Carbon::today()->toDateString(),
                status: 'Completed',
                notes: $request->input('notes')
            );
            $payout = $this->staffPayoutService->processPayout($dto);

            return redirect()
                ->route('admin.staff-payouts.show', $payout)
                ->with('banner', 'Payout processed successfully.')
                ->with('bannerStyle', 'success');
        } catch (\Exception $e) {
            return back()->with('banner', $e->getMessage())->with('bannerStyle', 'danger');
        }
    }

    public function revert(Request $request, StaffPayout $staff_payout)
    {
        $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $payout = $staff_payout->load('visitServices');

        DB::transaction(function () use ($payout, $request) {
            // Mark related visits as unpaid again
            if ($payout->visitServices && $payout->visitServices->count() > 0) {
                \App\Models\VisitService::whereIn('id', $payout->visitServices->pluck('id'))
                    ->update(['is_paid_to_staff' => false]);

                // Detach visits from this payout to avoid double counting
                $payout->visitServices()->detach();
            }

            // Update payout status and notes to reflect reversal
            $reasonText = trim((string) $request->input('reason'));
            $payout->status = 'Voided';
            $payout->notes = trim(($payout->notes ? $payout->notes."\n" : '') . 'Reverted'.($reasonText ? ": {$reasonText}" : ''));
            $payout->reverted_by = auth()->id();
            $payout->reverted_reason = $reasonText ?: null;
            $payout->reverted_at = now();
            $payout->save();
        });

        return redirect()
            ->route('admin.staff-payouts.show', $payout)
            ->with('banner', 'Payout reverted. Unpaid amounts are now available for processing again.')
            ->with('bannerStyle', 'success');
    }

    public function printAll(Request $request)
    {
        // Centralized PDF using ExportableTrait + ExportConfig
        return $this->handlePrintAll($request, StaffPayout::class, ExportConfig::getStaffPayoutConfig());
    }

    public function show(StaffPayout $staff_payout)
    {
        $payout = $staff_payout->load(['staff', 'visitServices']);

        return Inertia::render('Admin/StaffPayouts/Show', [
            'staffPayout' => $payout,
        ]);
    }

    public function edit(StaffPayout $staff_payout)
    {
        $payout = $staff_payout->load(['staff']);

        return Inertia::render('Admin/StaffPayouts/Edit', [
            'staffPayout' => $payout,
        ]);
    }

    public function update(Request $request, StaffPayout $staff_payout)
    {
        $validated = $request->validate(StaffPayoutRules::update($staff_payout));

        $staff_payout->fill($validated);
        $staff_payout->save();

        return redirect()->route('admin.staff-payouts.show', $staff_payout)->with('banner', 'Payout updated.')->with('bannerStyle', 'success');
    }
}
