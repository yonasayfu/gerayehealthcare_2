<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TestController extends Controller
{
    public function testAuth(Request $request)
    {
        $user = User::where('email', 'superadmin@test.com')->first();
        
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        
        $canViewAny = Gate::forUser($user)->allows('viewAny', User::class);
        $hasRole = $user->hasRole('super-admin');
        $roles = $user->roles->pluck('name');
        
        return response()->json([
            'user_id' => $user->id,
            'has_super_admin_role' => $hasRole,
            'roles' => $roles,
            'can_view_any_users' => $canViewAny,
            'all_permissions' => $user->permissions->pluck('name')
        ]);
    }
}