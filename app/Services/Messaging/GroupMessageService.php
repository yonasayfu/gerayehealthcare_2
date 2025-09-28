<?php

namespace App\Services\Messaging;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\GroupMessage;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GroupMessageService
{
    public function listGroupMessages(Group $group, int $page = 1, int $perPage = 50)
    {
        return GroupMessage::with([
            'sender:id,name,profile_photo_url',
            'replyTo:id,message,sender_id,created_at',
            'reactions' => function ($query) {
                $query->select('id', 'group_message_id', 'user_id', 'emoji');
            },
        ])
            ->where('group_id', $group->id)
            ->orderBy('created_at')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function storeMessage(Group $group, array $data): GroupMessage
    {
        return DB::transaction(function () use ($group, $data) {
            $path = null;
            $filename = null;
            $mime = null;
            if (! empty($data['attachment'])) {
                $file = $data['attachment'];
                $path = $file->store('group-messages/attachments', 'public');
                $filename = $file->getClientOriginalName();
                $mime = $file->getClientMimeType();
            }

            $msg = GroupMessage::create([
                'group_id' => $group->id,
                'sender_id' => Auth::id(),
                'message' => $data['message'] ?? null,
                'reply_to_id' => $data['reply_to_id'] ?? null,
                'attachment_path' => $path,
                'attachment_filename' => $filename,
                'attachment_mime_type' => $mime,
                'priority' => $data['priority'] ?? 'normal',
                'message_type' => $data['message_type'] ?? ($path ? 'file' : 'text'),
            ]);

            $group->touch();
            $this->clearGroupCaches($group->id);

            return $msg->load('sender:id,name,profile_photo_url');
        });
    }

    public function updateMessage(Group $group, GroupMessage $message, array $data): GroupMessage
    {
        $message->update([
            'message' => $data['message'],
        ]);

        return $message;
    }

    public function deleteMessage(Group $group, GroupMessage $message): void
    {
        // Remove polymorphic reactions first to avoid FK constraint issues
        if (method_exists($message, 'reactions')) {
            $message->reactions()->delete();
        }
        if ($message->attachment_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($message->attachment_path);
        }

        $message->delete();
    }

    public function pinMessage(GroupMessage $message): GroupMessage
    {
        $message->forceFill(['is_pinned' => true])->save();

        return $message->fresh();
    }

    public function unpinMessage(GroupMessage $message): GroupMessage
    {
        $message->forceFill(['is_pinned' => false])->save();

        return $message->fresh();
    }

    /**
     * @return array{path:string, name:string, headers:array}
     *
     * @throws FileNotFoundException
     */
    public function prepareDownload(Group $group, GroupMessage $message): array
    {
        if (! $message->attachment_path || ! Storage::disk('public')->exists($message->attachment_path)) {
            throw new FileNotFoundException('Attachment not found');
        }

        $name = $message->attachment_filename ?: basename($message->attachment_path);
        $headers = $message->attachment_mime_type ? ['Content-Type' => $message->attachment_mime_type] : [];

        return [
            'path' => $message->attachment_path,
            'name' => $name,
            'headers' => $headers,
        ];
    }

    public function getUserGroups(): \Illuminate\Support\Collection
    {
        $userId = Auth::id();
        $cacheKey = "user_groups_{$userId}";

        return Cache::remember($cacheKey, 60, function () {
            return Auth::user()->groups()
                ->withCount('members')
                ->with(['latestMessage' => function ($query) {
                    $query->with('sender:id,name,profile_photo_url')->latest();
                }, 'users:id,name,email'])
                ->orderBy('updated_at', 'desc')
                ->get();
        });
    }

    public function createGroup(array $data): Group
    {
        return DB::transaction(function () use ($data) {
            $group = Group::create([
                'name' => $data['name'],
                'created_by' => Auth::id(),
                'description' => $data['description'] ?? null,
            ]);

            $memberIds = collect($data['members'])
                ->push(Auth::id())
                ->unique()
                ->values();

            foreach ($memberIds as $memberId) {
                $group->users()->attach($memberId, [
                    'role' => $memberId === Auth::id() ? 'owner' : 'member',
                ]);
            }

            $this->clearGroupCaches($group->id, $memberIds->all());

            return $group
                ->load(['users:id,name,email'])
                ->loadCount('members');
        });
    }

    protected function clearGroupCaches(int $groupId, ?array $userIds = null): void
    {
        $memberIds = $userIds ?? GroupMember::where('group_id', $groupId)->pluck('user_id')->all();

        foreach ($memberIds as $memberId) {
            Cache::forget("group_member_{$groupId}_{$memberId}");
            Cache::forget("user_groups_{$memberId}");
        }
    }
}
