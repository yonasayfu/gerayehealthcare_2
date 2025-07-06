<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $query = Staff::query();

        if ($request->filled('search')) {
            $query->where('first_name', 'Ilike', '%' . $request->search . '%')
                ->orWhere('last_name', 'Ilike', '%' . $request->search . '%')
                ->orWhere('email', 'Ilike', '%' . $request->search . '%');
        }

        if ($request->filled('sort')) {
            $query->orderBy($request->sort, $request->get('direction', 'asc'));
        }

        return Inertia::render('Admin/Staff/Index', [
            'staff' => $query->paginate(10)->withQueryString(),
            'filters' => $request->only(['search', 'sort', 'direction']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Staff/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff,email',
            'phone' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'status' => 'required|string',
            'hire_date' => 'nullable|date',
            'photo' => 'nullable|image|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('images/staff', 'public');
        }

        Staff::create($request->except('photo') + ['photo' => $path]);

        return redirect()->route('admin.staff.index')->with('success', 'Staff member created successfully.');
    }

    public function show(Staff $staff)
    {
        return Inertia::render('Admin/Staff/Show', [
            'staff' => $staff,
        ]);
    }

    public function edit(Staff $staff)
    {
        return Inertia::render('Admin/Staff/Edit', [
            'staff' => $staff,
        ]);
    }

    public function update(Request $request, Staff $staff)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff,email,' . $staff->id,
            'phone' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'status' => 'required|string',
            'hire_date' => 'nullable|date',
            'photo' => 'nullable|image|max:2048',
        ]);

        $data = $request->except('photo');

        if ($request->hasFile('photo')) {
            if ($staff->photo) {
                Storage::disk('public')->delete($staff->photo);
            }
            $data['photo'] = $request->file('photo')->store('images/staff', 'public');
        }

        $staff->update($data);

        return redirect()->route('admin.staff.index')->with('success', 'Staff member updated successfully.');
    }

    public function destroy(Staff $staff)
    {
        if ($staff->photo) {
            Storage::disk('public')->delete($staff->photo);
        }
        $staff->delete();

        return back()->with('success', 'Staff member deleted successfully.');
    }
    public function export(Request $request)
{
    $type = $request->get('type');

    $staff = Staff::select('first_name', 'last_name', 'email', 'phone', 'position', 'department', 'status', 'hire_date')->get();

    if ($type === 'csv') {
        $csvData = "Full Name,Email,Phone,Position,Department,Status,Hire Date\n";
        foreach ($staff as $s) {
            $csvData .= "\"{$s->first_name} {$s->last_name}\",\"{$s->email}\",\"{$s->phone}\",\"{$s->position}\",\"{$s->department}\",\"{$s->status}\",\"{$s->hire_date}\"\n";
        }

        return \Response::make($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=\"staff.csv\"',
        ]);
    }

    if ($type === 'pdf') {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.staff', ['staff' => $staff])  // ✅ Corrected path
            ->setPaper('a4', 'landscape');

        return $pdf->stream('staff.pdf', [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename=\"staff.pdf\"',
        ]);
    }

    return abort(400, 'Invalid export type');
}
}
