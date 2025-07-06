<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with('roles'); // Eager load roles to prevent N+1 queries

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        return Inertia::render('Admin/Users/Index', [
            'users' => $query->paginate(10)->withQueryString(),
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new resource (Staff Registration).
     */
    public function create()
    {
        return Inertia::render('Admin/Users/Create');
    }

    /**
     * Store a newly created resource in storage.
     * This method handles the combined registration of a User and a Staff profile.
     */
    public function store(Request $request)
    {
        $request->validate([
            // User fields
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],

            // Staff fields
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'hire_date' => 'nullable|date',
        ]);

        // Use a database transaction to ensure both records are created successfully.
        // If one fails, the other is rolled back.
        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $staff = $user->staff()->create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email, // Ensure staff email matches user email
                'phone' => $request->phone,
                'position' => $request->position,
                'department' => $request->department,
                'hire_date' => $request->hire_date,
            ]);

            // Assign the default 'Staff' role
            $user->assignRole('Staff');
        });

        return redirect()->route('admin.users.index')->with('success', 'New staff user created successfully.');
    }

    /**
     * Show the form for editing the specified resource (Assigning Roles).
     */
    public function edit(User $user)
    {
        // Eager load the user's current roles
        $user->load('roles');
        
        return Inertia::render('Admin/Users/Edit', [
            'user' => $user,
            // Pass all available roles to the form for the dropdown
            'roles' => Role::all()->pluck('name'),
        ]);
    }

    /**
     * Update the specified resource in storage (Assigning Roles).
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);

        // Use syncRoles to remove old roles and assign the new one.
        $user->syncRoles([$request->role]);

        return redirect()->route('admin.users.index')->with('success', 'User role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Safety check to prevent the Super Admin from deleting their own account
        if ($user->hasRole('Super Admin') && $user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own Super Admin account.');
        }

        // The Staff profile will be deleted automatically because of the database relationship
        $user->delete();

        return back()->with('success', 'User deleted successfully.');
    }
}
