<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class StaffController extends Controller
{
      public function index(Request $request): \Inertia\Response
    {
        $query = Staff::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('first_name', 'ilike', "%{$search}%")
                  ->orWhere('last_name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%");
        }

        if ($request->has('sort')) {
            $query->orderBy($request->input('sort'), $request->input('direction', 'asc'));
        }

        // Using paginate() for full pagination links
        $staff = $query->paginate($request->input('per_page', 5))->withQueryString();

        return Inertia::render('Admin/Staff/Index', [
            'staff' => $staff,
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
            'hourly_rate' => 'nullable|numeric|min:0', 
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('images/staff', 'public');
        }

        Staff::create($validated);

        return redirect()->route('admin.staff.index')->with('success', 'Staff created successfully.');
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
            'hourly_rate' => 'nullable|numeric|min:0',
        ]);

        if ($request->hasFile('photo')) {
            if ($staff->photo && Storage::disk('public')->exists($staff->photo)) {
                Storage::disk('public')->delete($staff->photo);
            }
            $validated['photo'] = $request->file('photo')->store('images/staff', 'public');
        }

        $staff->update($validated);

        return redirect()->route('admin.staff.index')->with('success', 'Staff updated successfully.');
    }

    public function destroy(Staff $staff)
    {
        $staff->delete();

        return redirect()->route('admin.staff.index')->with('success', 'Staff deleted.');
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

    $staff = Staff::select('first_name', 'last_name', 'email', 'phone', 'position', 'department', 'status', 'hire_date')->get();

    if ($type === 'csv') {
        $csvData = "Full Name,Email,Phone,Position,Department,Status,Hire Date\n";
        foreach ($staff as $s) {
            $csvData .= "\"{$s->first_name} {$s->last_name}\",\"{$s->email}\",\"{$s->phone}\",\"{$s->position}\",\"{$s->department}\",\"{$s->status}\",\"{$s->hire_date}\"\n";
        }

        return \Response::make($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="staff.csv"',
        ]);
    }

    if ($type === 'pdf') {
        $data = $staff->map(function($s) {
            return [
                'full_name' => $s->first_name . ' ' . $s->last_name,
                'email' => $s->email ?? '-',
                'phone' => $s->phone ?? '-',
                'position' => $s->position ?? '-',
                'department' => $s->department ?? '-',
                'status' => $s->status,
                'hire_date' => \Carbon\Carbon::parse($s->hire_date)->format('Y-m-d'),
            ];
        })->toArray();

        $columns = [
            ['key' => 'full_name', 'label' => 'Full Name'],
            ['key' => 'email', 'label' => 'Email'],
            ['key' => 'phone', 'label' => 'Phone'],
            ['key' => 'position', 'label' => 'Position'],
            ['key' => 'department', 'label' => 'Department'],
            ['key' => 'status', 'label' => 'Status'],
            ['key' => 'hire_date', 'label' => 'Hire Date'],
        ];

        $title = 'Staff Export - Geraye Home Care Services';
        $documentTitle = 'All Staff Records';

        $pdf = Pdf::loadView('print-layout', compact('title', 'data', 'columns', 'documentTitle'))
                    ->setPaper('a4', 'landscape');
        return $pdf->stream('staff.pdf');
    }

    return abort(400, 'Invalid export type');
}

}
