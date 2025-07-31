<?php

namespace App\Http\Controllers\Insurance;

use App\Http\Controllers\Controller;
use App\Models\EthiopianCalendarDay;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;

class EthiopianCalendarDayController extends Controller
{
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

        $ethiopianCalendarDays = $query->paginate($request->input('per_page', 10))->withQueryString();

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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'gregorian_date' => 'required|date',
            'ethiopian_date' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_holiday' => 'required|boolean',
            'region' => 'nullable|string|max:255',
        ]);

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
    public function update(Request $request, string $id)
    {
        $ethiopianCalendarDay = EthiopianCalendarDay::findOrFail($id);

        $validated = $request->validate([
            'gregorian_date' => 'required|date',
            'ethiopian_date' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_holiday' => 'required|boolean',
            'region' => 'nullable|string|max:255',
        ]);

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
        $type = $request->get('type');
        $ethiopianCalendarDays = EthiopianCalendarDay::select('gregorian_date', 'ethiopian_date', 'description', 'is_holiday', 'region')->get();

        if ($type === 'csv') {
            $csvData = "Gregorian Date,Ethiopian Date,Description,Is Holiday,Region\n";
            foreach ($ethiopianCalendarDays as $day) {
                $csvData .= "\"{$day->gregorian_date}\",\"{$day->ethiopian_date}\",\"{$day->description}\",\"{$day->is_holiday}\",\"{$day->region}\"\n";
            }

            return Response::make($csvData, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="ethiopian_calendar_days.csv"',
            ]);
        }

        if ($type === 'pdf') {
            $pdf = Pdf::loadView('pdf.ethiopian_calendar_days', ['ethiopianCalendarDays' => $ethiopianCalendarDays])->setPaper('a4', 'landscape');
            return $pdf->stream('ethiopian_calendar_days.pdf');
        }

        return abort(400, 'Invalid export type');
    }

    public function printSingle(EthiopianCalendarDay $ethiopianCalendarDay)
    {
        $pdf = Pdf::loadView('pdf.ethiopian_calendar_day_single', ['ethiopianCalendarDay' => $ethiopianCalendarDay])->setPaper('a4', 'portrait');
        return $pdf->stream("ethiopian_calendar_day-{$ethiopianCalendarDay->id}.pdf");
    }

    public function printCurrent(Request $request)
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

        $ethiopianCalendarDays = $query->paginate($request->input('per_page', 10))->appends($request->except('page'));

        return Inertia::render('Insurance/EthiopianCalendarDays/PrintCurrent', ['ethiopianCalendarDays' => $ethiopianCalendarDays->items()]);
    }

    public function printAll(Request $request)
    {
        $ethiopianCalendarDays = EthiopianCalendarDay::orderBy('gregorian_date')->get();

        $pdf = Pdf::loadView('pdf.ethiopian_calendar_days', ['ethiopianCalendarDays' => $ethiopianCalendarDays])->setPaper('a4', 'landscape');
        return $pdf->stream('ethiopian_calendar_days.pdf');
    }
}
