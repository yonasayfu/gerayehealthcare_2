<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $groups = Group::with(['members.user:id,name'])
            ->whereHas('members', fn ($q) => $q->where('user_id', $userId))
            ->orderBy('name')
            ->get();

        return response()->json(['data' => $groups]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'members' => ['array'],
            'members.*' => ['integer', 'exists:users,id'],
        ]);

        // Create the group
        $group = Group::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'created_by' => Auth::id(),
        ]);

        // Add the creator as owner
        GroupMember::create([
            'group_id' => $group->id,
            'user_id' => Auth::id(),
            'role' => 'owner',
        ]);

        // Add selected members
        foreach (($data['members'] ?? []) as $uid) {
            if ($uid === Auth::id()) {
                continue;
            } // Skip creator
            GroupMember::firstOrCreate([
                'group_id' => $group->id,
                'user_id' => $uid,
            ], [
                'role' => 'member',
            ]);
        }

        // Load the group with members for response
        $group->load('members.user:id,name,email');

        // For AJAX requests, return JSON response
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Group created successfully!',
                'data' => $group,
            ], 201);
        }

        // For regular form submissions, return Inertia response
        return back()->with('success', 'Group created successfully!')->with('group', $group);
    }
}
