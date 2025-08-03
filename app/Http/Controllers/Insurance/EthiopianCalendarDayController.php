<?php

namespace App\Http\Controllers\Insurance;

use App\Http\Controllers\Controller;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\AdditionalExportConfigs;
use App\Models\EthiopianCalendarDay;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;
use App\Http\Requests\StoreEthiopianCalendarDayRequest;
use App\Http\Requests\UpdateEthiopianCalendarDayRequest;

class EthiopianCalendarDayController extends Controller
{
    use ExportableTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = EthiopianCalendarDay::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('description', 'ilike', "%{$search}%")
                  ->orWhere('ethiopian_date', 'ilike', "%{$search}%");
        }

        if ($request->filled('sort') && !empty($request->input('sort'))) {
            $query->orderBy($request->input('sort'), $request->input('direction', 'asc'));
        } else {
            $query->orderBy('gregorian_date', 'desc');
        }

        $ethiopianCalendarDays = $query->paginate($request->input('per_page', 5))->withQueryString();

        return Inertia::render('Insurance/EthiopianCalendarDays/Index', [
            'ethiopianCalendarDays' => $ethiopianCalendarDays,
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Insurance/EthiopianCalendarDays/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEthiopianCalendarDayRequest $request)
    {
        $validated = $request->validated();

        EthiopianCalendarDay::create($validated);

        return Redirect::route('admin.ethiopian-calendar-days.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $ethiopianCalendarDay = EthiopianCalendarDay::findOrFail($id);
        return Inertia::render('Insurance/EthiopianCalendarDays/Show', [
            'ethiopianCalendarDay' => $ethiopianCalendarDay,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $ethiopianCalendarDay = EthiopianCalendarDay::findOrFail($id);
        return Inertia::render('Insurance/EthiopianCalendarDays/Edit', [
            'ethiopianCalendarDay' => $ethiopianCalendarDay,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEthiopianCalendarDayRequest $request, string $id)
    {
        $ethiopianCalendarDay = EthiopianCalendarDay::findOrFail($id);

        $validated = $request->validated();

        $ethiopianCalendarDay->update($validated);

        return Redirect::route('admin.ethiopian-calendar-days.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $ethiopianCalendarDay = EthiopianCalendarDay::findOrFail($id);

        $ethiopianCalendarDay->delete();

        return Redirect::route('admin.ethiopian-calendar-days.index');
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, EthiopianCalendarDay::class, AdditionalExportConfigs::getEthiopianCalendarDayConfig());
    }

    public function printSingle(EthiopianCalendarDay $ethiopianCalendarDay)
    {
        return $this->handlePrintSingle($ethiopianCalendarDay, AdditionalExportConfigs::getEthiopianCalendarDayConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, EthiopianCalendarDay::class, AdditionalExportConfigs::getEthiopianCalendarDayConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, EthiopianCalendarDay::class, AdditionalExportConfigs::getEthiopianCalendarDayConfig());
    }
}
