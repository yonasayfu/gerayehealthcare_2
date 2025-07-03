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

        return Inertia::render('Admin//Index', [
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
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|unique:staff,email',
        'phone' => 'nullable|string|max:20',
        'position' => 'nullable|string|max:255',
        'department' => 'nullable|string|max:255',
        'status' => 'required|in:Active,Inactive',
        'hire_date' => 'nullable|date',
        'photo' => 'nullable|image|max:2048', // 2MB max
    ]);

    if ($request->hasFile('photo')) {
        $validated['photo'] = $request->file('photo')->store('images/staff', 'public');
    }

    Staff::create($validated);
    return redirect()->route('staff.index')->with('success', 'Staff created successfully.');
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
        // Delete old photo if exists
        if ($staff->photo && Storage::disk('public')->exists($staff->photo)) {
            Storage::disk('public')->delete($staff->photo);
        }
        $validated['photo'] = $request->file('photo')->store('images/staff', 'public');
    }

    $staff->update($validated);
    return redirect()->route('staff.index')->with('success', 'Staff updated successfully.');
}
public function show(Staff $staff)
{
    return Inertia::render('Admin/Staff/Show', [
        'staff' => $staff,
    ]);
}


    public function destroy(Staff $staff)
    {
        $staff->delete();

        return redirect()->route('staff.index')->with('success', 'Staff deleted.');
    }
}
