<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $query = Staff::query();

        if ($request->filled('search')) {
            $query->where('first_name', 'ilike', "%{$request->search}%")
                  ->orWhere('last_name', 'ilike', "%{$request->search}%")
                  ->orWhere('email', 'ilike', "%{$request->search}%");
        }

        if ($request->filled('sort') && in_array($request->sort, ['first_name', 'email', 'phone'])) {
            $query->orderBy($request->sort, $request->get('direction', 'asc'));
        }

        $perPage = $request->get('per_page', 10);

        return Inertia::render('Admin/Staff/Index', [
            'staff' => $query->paginate($perPage)->appends($request->all()),
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Staff/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff,email',
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'status' => 'required|in:Active,Inactive',
            'hire_date' => 'nullable|date',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('images/staff', 'public');
        }

        Staff::create($validated);

        return redirect()->route('staff.index')->with('success', 'Staff created successfully.');
    }

    public function edit(Staff $staff)
    {
        return Inertia::render('Admin/Staff/Edit', [
            'staff' => $staff,
        ]);
    }

    public function update(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff,email,' . $staff->id,
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'status' => 'required|in:Active,Inactive',
            'hire_date' => 'nullable|date',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($staff->photo && Storage::disk('public')->exists($staff->photo)) {
                Storage::disk('public')->delete($staff->photo);
            }
            $validated['photo'] = $request->file('photo')->store('images/staff', 'public');
        }

        $staff->update($validated);

        return redirect()->route('staff.index')->with('success', 'Staff updated successfully.');
    }

    public function destroy(Staff $staff)
    {
        $staff->delete();

        return redirect()->route('staff.index')->with('success', 'Staff deleted.');
    }

    public function show(Staff $staff)
    {
        return Inertia::render('Admin/Staff/Show', [
            'staff' => $staff,
        ]);
    }

    public function export(Request $request)
    {
        $type = $request->get('type');

        $staff = Staff::select('first_name', 'last_name', 'email', 'phone', 'position', 'status')->get();

        if ($type === 'csv') {
            $csvData = "First Name,Last Name,Email,Phone,Position,Status\n";
            foreach ($staff as $s) {
                $csvData .= "\"{$s->first_name}\",\"{$s->last_name}\",\"{$s->email}\",\"{$s->phone}\",\"{$s->position}\",\"{$s->status}\"\n";
            }

            return Response::make($csvData, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="staff.csv"',
            ]);
        }

        if ($type === 'pdf') {
            $pdf = Pdf::loadView('pdf.staff', ['staff' => $staff])
                      ->setPaper('a4', 'landscape');
            return $pdf->stream('staff.pdf', [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="staff.pdf"',
            ]);
        }

        return abort(400, 'Invalid export type');
    }
}
