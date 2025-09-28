<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Messaging\GroupMessageService;
use App\Http\Resources\GroupResource;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        $groups = app(GroupMessageService::class)->getUserGroups();

        if ($search = trim((string) $request->input('search', ''))) {
            $groups = $groups->filter(function ($group) use ($search) {
                return str_contains(strtolower($group->name), strtolower($search));
            });
        }

        if ($groups->isNotEmpty()) {
            $groups->load(['users:id,name,email']);
        }

        return GroupResource::collection($groups->values());
    }

    public function store(Request $request)
    {
        // Authorization: allow any authenticated user to create groups (per OriginalIssue.md)
        abort_unless(Auth::check(), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'members' => ['array'],
            'members.*' => ['integer', 'exists:users,id'],
        ]);

        $data['members'] = $data['members'] ?? [];
        $group = app(GroupMessageService::class)->createGroup($data);

        if ($request->expectsJson()) {
            return (new GroupResource($group))->response()->setStatusCode(201);
        }

        return back()->with('success', 'Group created successfully!');
    }
}
