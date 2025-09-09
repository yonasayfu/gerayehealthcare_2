<?php

namespace App\Http\Controllers;

use App\Http\Controllers\OptimizedBaseController;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GroupController extends OptimizedBaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get all groups for the authenticated user
     */
    public function index(Request $request)
    {
        try {
            $userId = Auth::id();
            
            $groups = Group::with(['members.user:id,name,email', 'creator:id,name'])
                ->whereHas('members', fn($q) => $q->where('user_id', $userId))
                ->orderBy('name')
                ->get()
                ->map(function ($group) {
                    return [
                        'id' => $group->id,
                        'name' => $group->name,
                        'description' => $group->description,
                        'member_count' => $group->members->count(),
                        'creator' => $group->creator,
                        'latest_message' => $group->latest_message,
                        'is_private' => $group->is_private,
                    ];
                });

            return $this->success($groups, 'Groups retrieved successfully');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to retrieve groups');
        }
    }

    /**
     * Create a new group
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string', 'max:1000'],
                'is_private' => ['boolean'],
                'max_members' => ['integer', 'min:2', 'max:1000'],
                'member_ids' => ['array'],
                'member_ids.*' => ['integer', 'exists:users,id'],
            ]);

            $group = DB::transaction(function () use ($validated) {
                $group = Group::create([
                    'name' => $validated['name'],
                    'description' => $validated['description'] ?? null,
                    'created_by' => Auth::id(),
                    'is_private' => $validated['is_private'] ?? false,
                    'max_members' => $validated['max_members'] ?? 100,
                ]);

                // Add creator as owner
                GroupMember::create([
                    'group_id' => $group->id,
                    'user_id' => Auth::id(),
                    'role' => GroupMember::ROLE_OWNER,
                    'joined_at' => now(),
                ]);

                // Add other members
                foreach (($validated['member_ids'] ?? []) as $memberId) {
                    if ($memberId !== Auth::id()) {
                        GroupMember::firstOrCreate([
                            'group_id' => $group->id,
                            'user_id' => $memberId,
                        ], [
                            'role' => GroupMember::ROLE_MEMBER,
                            'joined_at' => now(),
                        ]);
                    }
                }

                return $group;
            });

            return $this->success(
                $group->load(['members.user:id,name,email', 'creator:id,name']),
                'Group created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to create group');
        }
    }

    /**
     * Get a specific group with members
     */
    public function show(Group $group)
    {
        try {
            // Check if user is a member
            if (!$group->hasMember(Auth::id())) {
                return $this->error('You are not a member of this group', 403);
            }

            $group->load(['members.user:id,name,email,profile_photo_path', 'creator:id,name']);

            return $this->success($group, 'Group retrieved successfully');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to retrieve group');
        }
    }

    /**
     * Update a group
     */
    public function update(Request $request, Group $group)
    {
        try {
            // Check if user is admin
            if (!$group->isAdmin(Auth::id())) {
                return $this->error('You do not have permission to update this group', 403);
            }

            $validated = $request->validate([
                'name' => ['string', 'max:255'],
                'description' => ['nullable', 'string', 'max:1000'],
                'is_private' => ['boolean'],
                'max_members' => ['integer', 'min:2', 'max:1000'],
            ]);

            $group->update($validated);

            return $this->success($group, 'Group updated successfully');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to update group');
        }
    }

    /**
     * Add member to group
     */
    public function addMember(Request $request, Group $group)
    {
        try {
            // Check if user is admin
            if (!$group->isAdmin(Auth::id())) {
                return $this->error('You do not have permission to add members', 403);
            }

            $validated = $request->validate([
                'user_id' => ['required', 'integer', 'exists:users,id'],
                'role' => ['string', 'in:member,admin'],
            ]);

            // Check if user is already a member
            if ($group->hasMember($validated['user_id'])) {
                return $this->error('User is already a member of this group', 400);
            }

            // Check member limit
            if ($group->members()->count() >= $group->max_members) {
                return $this->error('Group has reached maximum member limit', 400);
            }

            $member = GroupMember::create([
                'group_id' => $group->id,
                'user_id' => $validated['user_id'],
                'role' => $validated['role'] ?? GroupMember::ROLE_MEMBER,
                'joined_at' => now(),
            ]);

            return $this->success(
                $member->load('user:id,name,email'),
                'Member added successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to add member');
        }
    }

    /**
     * Remove member from group
     */
    public function removeMember(Request $request, Group $group, User $user)
    {
        try {
            $authUser = Auth::user();
            
            // Check permissions
            if (!$group->isAdmin($authUser->id) && $authUser->id !== $user->id) {
                return $this->error('You do not have permission to remove this member', 403);
            }

            $member = GroupMember::where('group_id', $group->id)
                ->where('user_id', $user->id)
                ->first();

            if (!$member) {
                return $this->error('User is not a member of this group', 404);
            }

            // Prevent removing the last owner
            if ($member->isOwner() && $group->owners()->count() <= 1) {
                return $this->error('Cannot remove the last owner of the group', 400);
            }

            $member->delete();

            return $this->success(null, 'Member removed successfully');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to remove member');
        }
    }

    /**
     * Leave a group
     */
    public function leave(Group $group)
    {
        try {
            $userId = Auth::id();
            
            $member = GroupMember::where('group_id', $group->id)
                ->where('user_id', $userId)
                ->first();

            if (!$member) {
                return $this->error('You are not a member of this group', 404);
            }

            // Prevent last owner from leaving
            if ($member->isOwner() && $group->owners()->count() <= 1) {
                return $this->error('Cannot leave group as the last owner. Transfer ownership first.', 400);
            }

            $member->delete();

            return $this->success(null, 'Left group successfully');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to leave group');
        }
    }

    /**
     * Delete a group
     */
    public function destroy(Group $group)
    {
        try {
            // Only owners can delete groups
            if (!$group->isOwner(Auth::id())) {
                return $this->error('You do not have permission to delete this group', 403);
            }

            $group->delete();

            return $this->success(null, 'Group deleted successfully');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to delete group');
        }
    }
}
