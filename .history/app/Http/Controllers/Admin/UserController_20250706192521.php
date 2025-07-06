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
        $query = User::with('roles');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        return Inertia::render('Admin/Users/Index', [
            'users' => $query->paginate($request->get('per_page', 5))->withQueryString(), // Default to 5
            'filters' => $request->only(['search', 'per_page']),
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
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'hire_date' => 'nullable|date',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->staff()->create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'position' => $request->position,
                'department' => $request->department,
                'hire_date' => $request->hire_date,
            ]);

            $user->assignRole('Staff');
        });

        return redirect()->route('admin.users.index')->with('success', 'New staff user created successfully.');
    }

    /**
     * Show the form for editing the specified resource (Assigning Roles).
     */
    public function edit(User $user)
    {
        $user->load('roles');
        
        return Inertia::render('Admin/Users/Edit', [
            'user' => $user,
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

        $user->syncRoles([$request->role]);

        return redirect()->route('admin.users.index')->with('success', 'User role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user->hasRole('Super Admin') && $user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own Super Admin account.');
        }

        $user->delete();

        return back()->with('success', 'User deleted successfully.');
    }
}
