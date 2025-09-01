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
            ->whereHas('members', fn($q) => $q->where('user_id', $userId))
            ->orderBy('name')
            ->get();
        return response()->json(['data' => $groups]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'member_ids' => ['array'],
            'member_ids.*' => ['integer','exists:users,id'],
        ]);
        $group = Group::create(['name' => $data['name'], 'created_by' => Auth::id()]);
        GroupMember::create(['group_id' => $group->id, 'user_id' => Auth::id(), 'role' => 'owner']);
        foreach (($data['member_ids'] ?? []) as $uid) {
            if ($uid === Auth::id()) continue;
            GroupMember::firstOrCreate(['group_id' => $group->id, 'user_id' => $uid], ['role' => 'member']);
        }
        return response()->json(['data' => $group->load('members.user')], 201);
    }
}

