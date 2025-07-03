<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortBy = $request->input('sortBy', 'created_at');
        $sortOrder = $request->input('sortOrder', 'desc');
        $perPage = $request->input('perPage', 10);

        $query = Staff::query();

        if ($search) {
            $query->where('first_name', 'ilike', "%{$search}%")
                  ->orWhere('last_name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%");
        }

        $staff = $query->orderBy($sortBy, $sortOrder)
                       ->paginate($perPage)
                       ->withQueryString();

        return Inertia::render('Staff/Index', [
            'staff' => $staff,
            'filters' => $request->only(['search', 'sortBy', 'sortOrder', 'perPage']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Staff/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:staff,email',
            'phone'      => 'nullable|string|max:20',
            'position'   => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'status'     => 'in:Active,Inactive',
            'hire_date'  => 'nullable|date',
            'photo'      => 'nullable|string',
        ]);

        Staff::create($validated);

        return redirect()->route('staff.index')->with('success', 'Staff created successfully.');
    }

    public function edit(Staff $staff)
    {
        return Inertia::render('Staff/Edit', [
            'staffMember' => $staff,
        ]);
    }

    public function update(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:staff,email,' . $staff->id,
            'phone'      => 'nullable|string|max:20',
            'position'   => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'status'     => 'in:Active,Inactive',
            'hire_date'  => 'nullable|date',
            'photo'      => 'nullable|string',
        ]);

        $staff->update($validated);

        return redirect()->route('staff.index')->with('success', 'Staff updated successfully.');
    }

    public function destroy(Staff $staff)
    {
        $staff->delete();

        return redirect()->route('staff.index')->with('success', 'Staff deleted.');
    }
}
