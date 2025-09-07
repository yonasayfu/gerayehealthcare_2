<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\GroupMessage;
use App\Models\Reaction;
use App\Services\Messaging\GroupMessageService;
use App\Services\Validation\Rules\MessageRules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class GroupMessageController extends Controller
{
    public function getGroups(Request $request)
    {
        $groups = app(GroupMessageService::class)->getUserGroups();
        return response()->json(['data' => \App\Http\Resources\GroupResource::collection($groups)]);
        return response()->json(['data' => $groups]);
    }

    protected function ensureMember(Group $group): void
    {
        $userId = Auth::id();
        $cacheKey = "group_member_{$group->id}_{$userId}";

        $isMember = Cache::remember($cacheKey, 600, function () use ($group, $userId) {
            return GroupMember::where('group_id', $group->id)
                ->where('user_id', $userId)
                ->exists();
        });

        abort_unless($isMember, 403);
    }

    public function index(Request $request, Group $group)
    {
        $this->ensureMember($group);

        $page = (int) $request->get('page', 1);
        $perPage = min((int) $request->get('per_page', 50), 100);
        $messages = app(GroupMessageService::class)->listGroupMessages($group, $page, $perPage);

        return response()->json([
            'data' => $messages->items(),
            'pagination' => [
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'per_page' => $messages->perPage(),
                'total' => $messages->total(),
                'has_more' => $messages->hasMorePages(),
            ],
        ]);
    }

    public function store(Request $request, Group $group)
    {
        $this->ensureMember($group);

        $data = $request->validate(MessageRules::groupMessageRules(), MessageRules::messages());
        $data['attachment'] = $request->file('attachment');

        $msg = app(GroupMessageService::class)->storeMessage($group, $data);
        broadcast(new \App\Events\NewMessage($msg->load('sender')));

        return response()->json(['data' => $msg->load('sender')], 201);
    }

    protected function clearGroupCaches($groupId)
    {
        // Clear group-related caches
        Cache::forget("group_member_{$groupId}_".Auth::id());

        // Clear user groups cache for all group members
        $memberIds = GroupMember::where('group_id', $groupId)->pluck('user_id');
        foreach ($memberIds as $memberId) {
            Cache::forget("user_groups_{$memberId}");
        }
    }

    public function react(Request $request, Group $group, GroupMessage $message)
    {
        $this->ensureMember($group);
        abort_unless($message->group_id === $group->id, 404);
        $data = $request->validate(['emoji' => ['required', 'string', 'max:16']]);
        $reaction = Reaction::firstOrCreate([
            'reactable_type' => GroupMessage::class,
            'reactable_id' => $message->id,
            'user_id' => Auth::id(),
            'emoji' => $data['emoji'],
        ]);

        broadcast(new \App\Events\MessageReacted($reaction));

        return response()->noContent();
    }

    public function update(Request $request, Group $group, GroupMessage $message)
    {
        $this->ensureMember($group);
        abort_unless($message->group_id === $group->id, 404);
        abort_unless($message->sender_id === Auth::id(), 403);

        $data = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $updated = app(GroupMessageService::class)->updateMessage($group, $message, $data);
        broadcast(new \App\Events\MessageUpdated($updated));

        return response()->json(['data' => $updated]);
    }

    public function destroy(Request $request, Group $group, GroupMessage $message)
    {
        $this->ensureMember($group);
        abort_unless($message->group_id === $group->id, 404);

        // Allow sender or group owner/admin to delete
        $userId = Auth::id();
        $role = GroupMember::where('group_id', $group->id)->where('user_id', $userId)->value('role');
        abort_unless($message->sender_id === $userId || in_array($role, ['owner', 'admin']), 403);

        broadcast(new \App\Events\MessageDeleted($message));

        app(GroupMessageService::class)->deleteMessage($group, $message);

        return response()->noContent();
    }

    /**
     * Secure download for group message attachments (members only).
     */
    public function downloadAttachment(Request $request, Group $group, GroupMessage $message)
    {
        $this->ensureMember($group);
        abort_unless($message->group_id === $group->id, 404);

        if (! $message->attachment_path) {
            return response()->json(['error' => 'No attachment for this message'], 404);
        }

        $dl = app(GroupMessageService::class)->prepareDownload($group, $message);

        return \Illuminate\Support\Facades\Storage::disk('public')->download($dl['path'], $dl['name'], $dl['headers']);
    }

    public function createGroup(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:groups,name'],
            'description' => ['nullable', 'string', 'max:500'],
            'members' => ['required', 'array', 'min:1'],
            'members.*' => ['integer', 'exists:users,id'],
        ]);

        $group = app(GroupMessageService::class)->createGroup($validated);

        return response()->json(['data' => new \App\Http\Resources\GroupResource($group)], 201);
    }
}
