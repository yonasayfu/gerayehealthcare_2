<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\GroupMessage;
use App\Models\Reaction;
use App\Services\Validation\Rules\MessageRules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class GroupMessageController extends Controller
{
    public function getGroups(Request $request)
    {
        $userId = Auth::id();
        $cacheKey = "user_groups_{$userId}";

        $groups = Cache::remember($cacheKey, 300, function () use ($userId) {
            return Auth::user()->groups()
                ->withCount('members')
                ->with(['latestMessage' => function ($query) {
                    $query->with('sender:id,name')->latest();
                }])
                ->orderBy('updated_at', 'desc')
                ->get();
        });

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

        $page = $request->get('page', 1);
        $perPage = min($request->get('per_page', 50), 100); // Limit max per page

        $messages = GroupMessage::with([
            'sender:id,name',
            'replyTo:id,message,sender_id,created_at',
            'reactions' => function ($query) {
                $query->select('id', 'group_message_id', 'user_id', 'emoji');
            },
        ])
            ->where('group_id', $group->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $messages->items(),
            'pagination' => [
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'per_page' => $messages->perPage(),
                'total' => $messages->total(),
                'has_more' => $messages->hasMorePages(),
            ]
        ]);
    }

    public function store(Request $request, Group $group)
    {
        $this->ensureMember($group);

        $data = $request->validate(MessageRules::groupMessageRules(), MessageRules::messages());

        // Validate message content
        $contentErrors = MessageRules::validateMessageContent($data);
        if (!empty($contentErrors)) {
            return response()->json(['errors' => $contentErrors], 422);
        }

        DB::beginTransaction();
        try {
            $path = null;
            $filename = null;
            $mime = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $path = $file->store('group-messages/attachments', 'public');
                $filename = $file->getClientOriginalName();
                $mime = $file->getClientMimeType();
            }

            $msg = GroupMessage::create([
                'group_id' => $group->id,
                'sender_id' => Auth::id(),
                'message' => isset($data['message']) ? MessageRules::sanitizeMessage($data['message']) : null,
                'reply_to_id' => $data['reply_to_id'] ?? null,
                'attachment_path' => $path,
                'attachment_filename' => $filename,
                'attachment_mime_type' => $mime,
                'priority' => $data['priority'] ?? 'normal',
                'message_type' => $data['message_type'] ?? ($path ? 'file' : 'text'),
            ]);

            // Update group's last activity
            $group->touch();

            // Clear relevant caches
            $this->clearGroupCaches($group->id);

            broadcast(new \App\Events\NewMessage($msg->load('sender')));

            DB::commit();

            return response()->json(['data' => $msg->load('sender')], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to send message'], 500);
        }
    }

    protected function clearGroupCaches($groupId)
    {
        // Clear group-related caches
        Cache::forget("group_member_{$groupId}_" . Auth::id());

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

        $message->update($data);

        broadcast(new \App\Events\MessageUpdated($message));

        return response()->json(['data' => $message]);
    }

    public function destroy(Request $request, Group $group, GroupMessage $message)
    {
        $this->ensureMember($group);
        abort_unless($message->group_id === $group->id, 404);

        // TODO: Allow group admins/owners to delete messages
        abort_unless($message->sender_id === Auth::id(), 403);

        broadcast(new \App\Events\MessageDeleted($message));

        $message->delete();

        return response()->noContent();
    }

    public function createGroup(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:groups,name'],
            'members' => ['required', 'array', 'min:1'],
            'members.*' => ['integer', 'exists:users,id'],
        ]);

        $group = Group::create([
            'name' => $validated['name'],
            'created_by' => Auth::id(),
        ]);

        // Attach members with 'member' role
        foreach ($validated['members'] as $memberId) {
            $group->users()->attach($memberId, ['role' => 'member']);
        }

        // Attach the creator as 'owner'
        $group->users()->attach(Auth::id(), ['role' => 'owner']);

        return response()->json(['data' => $group->load('users')], 201);
    }
}
