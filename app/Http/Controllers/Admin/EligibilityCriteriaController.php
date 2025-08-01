<?php

namespace App\Http\Controllers\Admin;

use App\Models\EligibilityCriteria;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;

class EligibilityCriteriaController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:' . \App\Enums\RoleEnum::SUPER_ADMIN->value . '|' . \App\Enums\RoleEnum::ADMIN->value);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = EligibilityCriteria::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('criteria_name', 'ilike', "%{$search}%");
        }

        if ($request->filled('sort') && !empty($request->input('sort'))) {
            $sortField = $request->input('sort');
            $sortDirection = $request->input('direction', 'asc');

            $sortableFields = ['criteria_name', 'operator', 'value', 'created_at'];
            if (in_array($sortField, $sortableFields)) {
                $query->orderBy($sortField, $sortDirection);
            } else {
                $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $criteria = $query->paginate($request->input('per_page', 5))->withQueryString();

        return Inertia::render('Admin/EligibilityCriteria/Index', [
            'criteria' => $criteria,
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/EligibilityCriteria/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'criteria_name' => 'required|string|max:255',
            'operator' => 'required|string|max:255',
            'value' => 'required|string|max:255',
        ]);

        EligibilityCriteria::create($validated);

        return redirect()->route('admin.eligibility-criteria.index')
            ->with('success', 'Eligibility criteria created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(EligibilityCriteria $eligibilityCriteria)
    {
        return Inertia::render('Admin/EligibilityCriteria/Show', [
            'eligibilityCriteria' => $eligibilityCriteria,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EligibilityCriteria $eligibilityCriteria)
    {
        return Inertia::render('Admin/EligibilityCriteria/Edit', [
            'eligibilityCriteria' => $eligibilityCriteria,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EligibilityCriteria $eligibilityCriteria)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'criteria_name' => 'required|string|max:255',
            'operator' => 'required|string|max:255',
            'value' => 'required|string|max:255',
        ]);

        $eligibilityCriteria->update($validated);

        return redirect()->route('admin.eligibility-criteria.index')
            ->with('success', 'Eligibility criteria updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EligibilityCriteria $eligibilityCriteria)
    {
        $eligibilityCriteria->delete();

        return redirect()->route('admin.eligibility-criteria.index')
            ->with('success', 'Eligibility criteria deleted successfully.');
    }

    public function export(Request $request)
    {
        $type = $request->get('type');
        $criteria = EligibilityCriteria::select('event_id', 'criteria_title', 'operator', 'value')->get();

        if ($type === 'csv') {
            $csvData = "Event ID,Criteria Title,Operator,Value\n";            foreach ($criteria as $criterion) {                $csvData .= "\"{$criterion->event_id}\",\"{$criterion->criteria_title}\",\"{$criterion->operator}\",\"{$criterion->value}\"\n";
            }

            return Response::make($csvData, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="eligibility-criteria.csv"',
            ]);
        }

        if ($type === 'pdf') {
            $pdf = Pdf::loadView('pdf.eligibility-criteria', ['criteria' => $criteria])->setPaper('a4', 'landscape');
            return $pdf->stream('eligibility-criteria.pdf');
        }

        return abort(400, 'Invalid export type');
    }

    public function printSingle(EligibilityCriteria $eligibilityCriteria)
    {
        $pdf = Pdf::loadView('pdf.eligibility-criteria-single', ['eligibilityCriteria' => $eligibilityCriteria])->setPaper('a4', 'portrait');
        return $pdf->stream("eligibility-criteria-{$eligibilityCriteria->id}.pdf");
    }

    public function printCurrent(Request $request)
    {
        $query = EligibilityCriteria::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('criteria_name', 'ilike', "%{$search}%");
        }

        if ($request->filled('sort') && !empty($request->input('sort'))) {
            $query->orderBy($request->input('sort'), $request->input('direction', 'asc'));
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $criteria = $query->paginate($request->input('per_page', 5))->appends($request->except('page'));

        return Inertia::render('Admin/EligibilityCriteria/PrintCurrent', ['criteria' => $criteria->items()]);
    }

    public function printAll(Request $request)
    {
        $criteria = EligibilityCriteria::orderBy('criteria_title')->get();

        $pdf = Pdf::loadView('pdf.eligibility-criteria', ['criteria' => $criteria])->setPaper('a4', 'landscape');
        return $pdf->stream('eligibility-criteria.pdf');
    }
}
