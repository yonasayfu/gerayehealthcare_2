<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Inertia\Inertia;
use PDF;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $patients = Patient::query()
            ->when($search, function ($query, $search) {
                $query->where('full_name', 'ILIKE', "%{$search}%")
                    ->orWhere('email', 'ILIKE', "%{$search}%")
                    ->orWhere('phone_number', 'ILIKE', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Patients/Index', [
            'patients' => $patients,
            'filters' => $request->only('search'),
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
     * 📤 Export data as CSV or PDF
     */
    public function export(Request $request)
    {
        $type = $request->input('type');
        $patients = Patient::latest()->get(['full_name', 'email', 'phone_number', 'gender', 'emergency_contact']);

        if ($type === 'csv') {
            $filename = 'patients_' . now()->format('Ymd_His') . '.csv';

            $headers = [
                'Content-type' => 'text/csv',
                'Content-Disposition' => "attachment; filename={$filename}",
            ];

            $callback = function () use ($patients) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['Name', 'Email', 'Phone', 'Gender', 'Emergency Contact']);

                foreach ($patients as $patient) {
                    fputcsv($handle, [
                        $patient->full_name,
                        $patient->email,
                        $patient->phone_number,
                        $patient->gender,
                        $patient->emergency_contact,
                    ]);
                }

                fclose($handle);
            };

            return new StreamedResponse($callback, 200, $headers);
        }

        if ($type === 'pdf') {
            $pdf = PDF::loadView('exports.patients', compact('patients'));
            return $pdf->download('patients_' . now()->format('Ymd_His') . '.pdf');
        }

        return back()->with('error', 'Invalid export type.');
    }
}
