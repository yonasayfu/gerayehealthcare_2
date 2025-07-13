<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;
use Illuminate\Validation\Rule; // Make sure this is imported

class PatientController extends Controller
{
    // ... index, create, export methods remain the same ...
    public function index(Request $request): \Inertia\Response
    {
        $query = Patient::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('full_name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%");

        }

        if ($request->filled('sort')) {
            $query->orderBy($request->input('sort'), $request->input('direction', 'asc'));
        }

        $patients = $query->paginate($request->input('per_page', 10))->withQueryString();

        return Inertia::render('Admin/Patients/Index', [
            'patients' => $patients,
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Patients/Create');
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'fayda_id' => 'nullable|string|max:255|unique:patients,fayda_id',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|max:10',
            'address' => 'nullable|string',
            'phone_number' => 'nullable|numeric',
            'email' => 'nullable|email|max:255',
            'emergency_contact' => 'nullable|string',
            'geolocation' => 'nullable|string',
        ]);

        Patient::create($data);

        return redirect()->route('admin.patients.index')->with('success', 'Patient created successfully.');
    }

    // --- START: NEW SHOW METHOD ---
    /**
     * Display the specified resource.
     */
    public function show(Patient $patient)
    {
        return Inertia::render('Admin/Patients/Show', [
            'patient' => $patient,
        ]);
    }
    // --- END: NEW SHOW METHOD ---

    public function edit(Patient $patient)
    {
        return Inertia::render('Admin/Patients/Edit', [
            'patient' => $patient,
        ]);
    }

    public function update(Request $request, Patient $patient)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'fayda_id' => ['nullable', 'string', 'max:255', Rule::unique('patients')->ignore($patient->id)],
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|max:10',
            'address' => 'nullable|string',
            'phone_number' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'emergency_contact' => 'nullable|string',
            'geolocation' => 'nullable|string',
        ]);

        $patient->update($data);

        return redirect()->route('admin.patients.index')->with('success', 'Patient updated successfully.');
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();
        return back()->with('success', 'Patient deleted successfully.');
    }

    public function export(Request $request)
    {
        $type = $request->get('type');
        $patients = Patient::select('full_name', 'email', 'phone_number', 'gender', 'emergency_contact')->get();

        if ($type === 'csv') {
            $csvData = "Full Name,Email,Phone,Gender,Emergency Contact\n";
            foreach ($patients as $p) {
                $csvData .= "\"{$p->full_name}\",\"{$p->email}\",\"{$p->phone_number}\",\"{$p->gender}\",\"{$p->emergency_contact}\"\n";
            }

            return Response::make($csvData, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="patients.csv"',
            ]);
        }

        if ($type === 'pdf') {
            $pdf = Pdf::loadView('pdf.patients', ['patients' => $patients])->setPaper('a4', 'landscape');
            return $pdf->stream('patients.pdf');
        }

        return abort(400, 'Invalid export type');
    }
}