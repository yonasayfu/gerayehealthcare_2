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
                ->orWhere('email', 'like', '%' . $request->search . '%');
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
}
