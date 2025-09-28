<?php

namespace App\Services\Messaging;

use App\Models\Group;
use App\Models\GroupMessage;
use App\Models\Message;
use App\Models\Reaction;
use App\Models\User;

class TelegramInboxService
{
    public const CONTEXT_DIRECT = 'direct';
    public const CONTEXT_CHANNEL = 'channel';

    private const ORGANIZATION_CHANNEL_NAME = 'Organization Telegram';
    private const ORGANIZATION_CHANNEL_DESCRIPTION = 'Company-wide updates and announcements';

    public function buildPayload(User $user, array $params = []): array
    {
        $context = $this->normalizeContext($params['context'] ?? null);
        $conversationId = $params['conversation_id'] ?? null;
        $search = trim((string) ($params['search'] ?? ''));

        $organizationChannel = $this->ensureOrganizationChannel($user);

        $staffSection = $this->buildStaffSection($user, $search);
        $channelSection = $this->buildChannelSection($user, $organizationChannel, $search);

        $sections = array_values(array_filter([
            $staffSection,
            $channelSection,
        ], fn ($section) => ! empty($section['conversations'])));

        $selected = $this->resolveSelectedConversation($sections, $context, $conversationId);
        if ($selected) {
            $context = $selected['type'];
            // Reset unread counts for currently opened direct conversation
            if ($selected['type'] === self::CONTEXT_DIRECT) {
                $sections = $this->clearUnreadForConversation($sections, $selected['id']);
                $selected['unread'] = 0;
            }
        }

        $messages = $selected
            ? $this->loadMessagesForConversation($user, $selected)
            : [];

        return [
            'search' => $search,
            'context' => $context,
            'sections' => $sections,
            'selectedConversation' => $selected,
            'messages' => $messages,
        ];
    }

    public function transformDirectMessage(Message $message, int $currentUserId): array
    {
        // For direct messages, load the dedicated messageReactions relation (not polymorphic reactions)
        $message->loadMissing(['sender.staff', 'receiver.staff']);

        return $this->formatBaseMessage($message, $currentUserId) + [
            'context' => self::CONTEXT_DIRECT,
            'conversation_id' => $message->sender_id === $currentUserId ? $message->receiver_id : $message->sender_id,
            'group_id' => null,
            'receiver_id' => $message->receiver_id,
            'receiver' => $message->receiver ? $this->transformDirectConversation($message->receiver, 0, null) : null,
            'read_at' => optional($message->read_at)?->toIso8601String(),
            'is_pinned' => (bool) $message->is_pinned,
            'pinned_at' => $message->is_pinned ? optional($message->updated_at)?->toIso8601String() : null,
        ];
    }

    public function transformChannelMessage(GroupMessage $message, int $currentUserId): array
    {
        $message->loadMissing(['sender.staff']);

        return [
            'id' => $message->id,
            'context' => self::CONTEXT_CHANNEL,
            'conversation_id' => $message->group_id,
            'group_id' => $message->group_id,
            'sender_id' => $message->sender_id,
            'receiver_id' => null,
            'message' => $message->message,
            'read_at' => null,
            'attachment_path' => $message->attachment_path,
            'attachment_filename' => $message->attachment_filename,
            'attachment_mime_type' => $message->attachment_mime_type,
            'attachment_url' => $message->attachment_url,
            'created_at' => optional($message->created_at)?->toIso8601String(),
            'updated_at' => optional($message->updated_at)?->toIso8601String(),
            'is_mine' => $message->sender_id === $currentUserId,
            'sender' => $this->transformDirectConversation($message->sender, 0, null),
            'receiver' => null,
            // reactions and reply_to removed from payload
            'is_pinned' => (bool) $message->is_pinned,
            'pinned_at' => $message->is_pinned ? optional($message->updated_at)?->toIso8601String() : null,
        ];
    }

    protected function normalizeContext(?string $context): string
    {
        return in_array($context, [self::CONTEXT_DIRECT, self::CONTEXT_CHANNEL], true)
            ? $context
            : self::CONTEXT_DIRECT;
    }

    protected function ensureOrganizationChannel(User $user): Group
    {
        $group = Group::firstOrCreate(
            ['name' => self::ORGANIZATION_CHANNEL_NAME],
            [
                'created_by' => $user->id,
                'description' => self::ORGANIZATION_CHANNEL_DESCRIPTION,
            ]
        );

        if (! $group->description) {
            $group->fill(['description' => self::ORGANIZATION_CHANNEL_DESCRIPTION]);
            $group->save();
        }

        $staffIds = User::query()
            ->whereHas('staff')
            ->pluck('id')
            ->push($user->id)
            ->unique()
            ->values();

        if ($staffIds->isNotEmpty()) {
            $group->users()->syncWithoutDetaching($staffIds->all());
        }

        return $group->loadCount('members');
    }

    protected function buildStaffSection(User $user, string $search): array
    {
        $authId = $user->id;

        $query = User::query()
            ->with('staff')
            ->where('id', '!=', $authId)
            ->whereHas('staff');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%")
                    ->orWhereHas('staff', function ($sq) use ($search) {
                        $sq->where('department', 'ilike', "%{$search}%")
                            ->orWhere('position', 'ilike', "%{$search}%");
                    });
            });
        }

        $staffUsers = $query->orderBy('name')->limit(200)->get();

        $recentMessages = Message::query()
            ->where(function ($q) use ($authId) {
                $q->where('sender_id', $authId)
                    ->orWhere('receiver_id', $authId);
            })
            ->orderByDesc('created_at')
            ->limit(500)
            ->get(['id', 'sender_id', 'receiver_id', 'created_at']);

        $lastMessageMap = [];
        foreach ($recentMessages as $message) {
            $counterpartId = $message->sender_id === $authId ? $message->receiver_id : $message->sender_id;
            if (! isset($lastMessageMap[$counterpartId])) {
                $lastMessageMap[$counterpartId] = optional($message->created_at)?->toIso8601String();
            }
        }

        $historyUserIds = collect($lastMessageMap)
            ->keys()
            ->map(fn ($key) => (int) $key)
            ->diff($staffUsers->pluck('id')->map(fn ($id) => (int) $id));

        if ($historyUserIds->isNotEmpty()) {
            $additionalUsers = User::with('staff')
                ->whereIn('id', $historyUserIds->all())
                ->get();

            $staffUsers = $staffUsers->concat($additionalUsers);
        }

        $unreadCounts = Message::selectRaw('sender_id, COUNT(*) as unread')
            ->where('receiver_id', $authId)
            ->whereNull('read_at')
            ->groupBy('sender_id')
            ->pluck('unread', 'sender_id');

        $conversations = $staffUsers->unique('id')
            ->map(function (User $contact) use ($unreadCounts, $lastMessageMap) {
                return $this->transformDirectConversation(
                    $contact,
                    (int) ($unreadCounts[$contact->id] ?? 0),
                    $lastMessageMap[$contact->id] ?? null
                );
            })
            ->sortByDesc(fn ($conversation) => $conversation['last_message_at'] ?? '')
            ->values()
            ->all();

        return [
            'key' => 'staff',
            'label' => 'Team',
            'type' => self::CONTEXT_DIRECT,
            'conversations' => $conversations,
        ];
    }

    protected function buildChannelSection(User $user, Group $organizationChannel, string $search): array
    {
        $groups = $user->groups()
            ->withCount('members')
            ->get();

        if (! $groups->contains('id', $organizationChannel->id)) {
            $groups->push($organizationChannel);
        }

        if ($search !== '') {
            $groups = $groups->filter(function (Group $group) use ($search) {
                $haystack = strtolower($group->name.' '.($group->description ?? ''));
                return str_contains($haystack, strtolower($search));
            });
        }

        $groupIds = $groups->pluck('id');

        $lastMessages = GroupMessage::query()
            ->whereIn('group_id', $groupIds)
            ->orderByDesc('created_at')
            ->get(['group_id', 'message', 'created_at'])
            ->groupBy('group_id')
            ->map(fn ($collection) => $collection->first());

        $conversations = $groups
            ->unique('id')
            ->map(function (Group $group) use ($organizationChannel, $lastMessages) {
                return $this->transformChannelConversation(
                    $group,
                    $group->id === $organizationChannel->id,
                    optional(optional($lastMessages->get($group->id))->created_at)?->toIso8601String()
                );
            })
            ->sortByDesc(fn ($conversation) => $conversation['last_message_at'] ?? '')
            ->values()
            ->all();

        return [
            'key' => 'channels',
            'label' => 'Channels',
            'type' => self::CONTEXT_CHANNEL,
            'conversations' => $conversations,
        ];
    }

    protected function resolveSelectedConversation(array $sections, string $context, $conversationId): ?array
    {
        $flattened = collect($sections)->flatMap(function ($section) {
            return collect($section['conversations'] ?? [])->map(function ($conversation) use ($section) {
                return $conversation + ['section' => $section['key']];
            });
        });

        if ($conversationId !== null) {
            $selected = $flattened->first(function ($conversation) use ($conversationId, $context) {
                return (int) $conversation['id'] === (int) $conversationId
                    && $conversation['type'] === $context;
            });

            if ($selected) {
                return $selected;
            }
        }

        $preferred = $flattened->first(fn ($conversation) => $conversation['type'] === $context);
        if ($preferred) {
            return $preferred;
        }

        return $flattened->first();
    }

    protected function clearUnreadForConversation(array $sections, int $conversationId): array
    {
        return array_map(function ($section) use ($conversationId) {
            if (! isset($section['conversations']) || $section['type'] !== self::CONTEXT_DIRECT) {
                return $section;
            }

            $section['conversations'] = array_map(function ($conversation) use ($conversationId) {
                if ((int) $conversation['id'] === (int) $conversationId) {
                    $conversation['unread'] = 0;
                }

                return $conversation;
            }, $section['conversations']);

            return $section;
        }, $sections);
    }

    protected function loadMessagesForConversation(User $user, array $conversation): array
    {
        if ($conversation['type'] === self::CONTEXT_DIRECT) {
            $counterpartId = (int) $conversation['id'];

            Message::where('sender_id', $counterpartId)
                ->where('receiver_id', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            $messages = Message::where(function ($query) use ($user, $counterpartId) {
                $query->where('sender_id', $user->id)
                    ->where('receiver_id', $counterpartId)
                    ->whereNull('deleted_for_sender_at');
            })
                ->orWhere(function ($query) use ($user, $counterpartId) {
                    $query->where('sender_id', $counterpartId)
                        ->where('receiver_id', $user->id)
                        ->whereNull('deleted_for_receiver_at');
                })
                ->with(['sender.staff', 'receiver.staff', 'messageReactions', 'replyTo'])
                ->orderBy('created_at', 'asc')
                ->get();

            return $messages->map(fn (Message $message) => $this->transformDirectMessage($message, $user->id))->values()->all();
        }

        if ($conversation['type'] === self::CONTEXT_CHANNEL) {
            $groupId = (int) $conversation['id'];

            $messages = GroupMessage::where('group_id', $groupId)
                ->with(['sender.staff'])
                ->orderBy('created_at', 'asc')
                ->get();

            return $messages->map(fn (GroupMessage $message) => $this->transformChannelMessage($message, $user->id))->values()->all();
        }

        return [];
    }

    public function transformDirectConversation(User $user, int $unread, ?string $lastMessageAt): array
    {
        $user->loadMissing('staff');
        $staff = $user->staff;

        $staffData = null;
        if ($staff) {
            $staffData = [
                'id' => $staff->id,
                'title' => $staff->title,
                'department' => $staff->department,
                'position' => $staff->position,
            ];
        }

        return [
            'id' => $user->id,
            'type' => self::CONTEXT_DIRECT,
            'name' => $user->name,
            'email' => $user->email,
            'profile_photo_url' => $user->profile_photo_url ?? null,
            'unread' => $unread,
            'last_message_at' => $lastMessageAt,
            'staff' => $staffData,
            'is_staff' => $staffData !== null,
        ];
    }

    public function transformChannelConversation(Group $group, bool $isOrganization, ?string $lastMessageAt): array
    {
        return [
            'id' => $group->id,
            'type' => self::CONTEXT_CHANNEL,
            'name' => $group->name,
            'description' => $group->description,
            'member_count' => $group->members_count ?? $group->members()->count(),
            'last_message_at' => $lastMessageAt,
            'is_organization' => $isOrganization,
        ];
    }

    public function isOrganizationChannel(Group $group): bool
    {
        return $group->name === self::ORGANIZATION_CHANNEL_NAME;
    }

    protected function formatBaseMessage(Message $message, int $currentUserId): array
    {
        return [
            'id' => $message->id,
            'sender_id' => $message->sender_id,
            'message' => $message->message,
            'attachment_path' => $message->attachment_path,
            'attachment_filename' => $message->attachment_filename,
            'attachment_mime_type' => $message->attachment_mime_type,
            'attachment_url' => $message->attachment_url,
            'created_at' => optional($message->created_at)?->toIso8601String(),
            'updated_at' => optional($message->updated_at)?->toIso8601String(),
            'is_mine' => $message->sender_id === $currentUserId,
            'sender' => $this->transformDirectConversation($message->sender, 0, null),
            // reactions and reply_to removed from payload

            'is_pinned' => (bool) $message->is_pinned,
            'pinned_at' => $message->is_pinned ? optional($message->updated_at)?->toIso8601String() : null,
        ];
    }
}
