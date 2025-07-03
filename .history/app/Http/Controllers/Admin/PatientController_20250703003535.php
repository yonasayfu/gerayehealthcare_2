<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;

class PatientController extends Controller
{

    public function index(Request $request)
    {
        $query = Patient::query();

        if ($request->filled('search')) {
            $query->where('full_name', 'Ilike', '%' . $request->search . '%')
                ->orWhere('email', 'Ilike', '%' . $request->search . '%')
                ->orWhere('phone_number', 'Ilike', '%' . $request->search . '%');
        }

        if ($request->filled('sort') && in_array($request->sort, ['full_name', 'email', 'phone_number'])) {
            $query->orderBy($request->sort, $request->get('direction', 'asc'));
        }

        $perPage = $request->get('per_page', 10);

        return Inertia::render('Admin/Patients/Index', [
            'patients' => $query->paginate($perPage)->appends($request->all()),
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
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|max:10',
            'address' => 'nullable|string',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            
            'emergency_contact' => 'nullable|string',
            'geolocation' => 'nullable|string',
        ]);

        Patient::create($data);

        return redirect()->route('patients.index');
    }

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
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|max:10',
            'address' => 'nullable|string',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'emergency_contact' => 'nullable|string',
            'geolocation' => 'nullable|string',
        ]);

        $patient->update($data);

        return redirect()->route('patients.index');
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();

        return back();
    }

    /**
    /**
     * 📤 Export data as CSV or PDF
     */
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
            // $pdf = Pdf::loadView('pdf.patients', ['patients' => $patients]);
            // return $pdf->download('patients.pdf');
            $pdf = Pdf::loadView('pdf.patients', ['patients' => $patients])
                ->setPaper('a4', 'landscape'); // 👈 Landscape mode
            return $pdf->stream('patients.pdf', [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="patients.pdf"',
            ]);
        }

        return abort(400, 'Invalid export type');
    }

}
