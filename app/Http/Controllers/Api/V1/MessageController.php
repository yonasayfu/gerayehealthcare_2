<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\SendMessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Models\User;
use App\Services\Messaging\MessageService;
use Illuminate\Http\Request;

class MessageController extends \App\Http\Controllers\Controller
{
    public function threads(Request $request)
    {
        $userId = $request->user()->id;

        // Find distinct counterpart user IDs in conversations
        $counterpartIds = Message::query()
            ->selectRaw('CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END as counterpart_id', [$userId])
            ->where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)->orWhere('receiver_id', $userId);
            })
            ->groupBy('counterpart_id')
            ->pluck('counterpart_id')
            ->filter();

        $counterparts = User::whereIn('id', $counterpartIds)->orderBy('name')->get();

        // Unread counts per counterpart
        $unreadCounts = Message::selectRaw('sender_id as user_id, COUNT(*) as unread')
            ->where('receiver_id', $userId)
            ->whereNull('read_at')
            ->groupBy('sender_id')
            ->pluck('unread', 'user_id');

        // Last message per counterpart for ordering
        $lastMessages = Message::selectRaw('CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END as uid, MAX(created_at) as last_at', [$userId])
            ->where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)->orWhere('receiver_id', $userId);
            })
            ->groupBy('uid')
            ->pluck('last_at', 'uid');

        $data = $counterparts->map(function ($u) use ($unreadCounts, $lastMessages) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'unread' => (int) ($unreadCounts[$u->id] ?? 0),
                'last_at' => $lastMessages[$u->id] ?? null,
            ];
        })->sortByDesc('last_at')->values();

        return response()->json(['data' => $data]);
    }

    public function thread(Request $request, User $user)
    {
        $this->authorize('communicate', $user);
        $authId = $request->user()->id;

        // Mark messages from other user to me as read
        Message::where('sender_id', $user->id)->where('receiver_id', $authId)->whereNull('read_at')->update(['read_at' => now()]);

        $messages = Message::with(['sender', 'receiver'])
            ->where(function ($q) use ($authId, $user) {
                $q->where('sender_id', $authId)->where('receiver_id', $user->id);
            })->orWhere(function ($q) use ($authId, $user) {
                $q->where('sender_id', $user->id)->where('receiver_id', $authId);
            })
            ->orderBy('created_at', 'asc')
            ->paginate($request->integer('per_page', 30));

        return MessageResource::collection($messages);
    }

    public function send(SendMessageRequest $request, User $user)
    {
        $this->authorize('communicate', $user);
        $data = [
            'message' => $request->validated()['message'] ?? null,
            'attachment' => $request->file('attachment'),
        ];
        $message = app(MessageService::class)->sendDirectMessage($user, $data);

        return new MessageResource($message->load(['sender', 'receiver']));
    }

    public function destroy(Message $message)
    {
        $user = request()->user();
        $canDelete = $message->sender_id === $user->id
            || $user->hasRole('super-admin')
            || $user->hasRole('admin')
            || $user->can('delete all messages');
        if (! $canDelete) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        app(MessageService::class)->deleteMessage($message);

        return response()->noContent();
    }

    public function bulkDestroy(Request $request)
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:messages,id'],
        ]);

        $user = $request->user();
        $service = app(MessageService::class);

        Message::whereIn('id', $data['ids'])->get()->each(function (Message $message) use ($user, $service) {
            $canDelete = $message->sender_id === $user->id
                || $user->hasRole('super-admin')
                || $user->hasRole('admin')
                || $user->can('delete all messages');

            abort_unless($canDelete, 403, 'Unauthorized to delete one or more messages.');

            $service->deleteMessage($message);
        });

        return response()->noContent();
    }

    public function pin(Message $message)
    {
        $user = request()->user();

        abort_unless($message->sender_id === $user->id
            || $message->receiver_id === $user->id
            || $user->hasRole('super-admin')
            || $user->hasRole('admin')
            || $user->can('edit all messages'), 403);

        $message = app(MessageService::class)->pinMessage($message)->load(['sender', 'receiver']);

        return new MessageResource($message);
    }

    public function unpin(Message $message)
    {
        $user = request()->user();

        abort_unless($message->sender_id === $user->id
            || $message->receiver_id === $user->id
            || $user->hasRole('super-admin')
            || $user->hasRole('admin')
            || $user->can('edit all messages'), 403);

        $message = app(MessageService::class)->unpinMessage($message)->load(['sender', 'receiver']);

        return new MessageResource($message);
    }

    public function search(Request $request, User $user)
    {
        $this->authorize('communicate', $user);

        $data = $request->validate([
            'q' => ['required', 'string', 'max:255'],
        ]);

        $authId = $request->user()->id;

        $messages = Message::query()
            ->where(function ($query) use ($authId, $user) {
                $query->where('sender_id', $authId)->where('receiver_id', $user->id);
            })
            ->orWhere(function ($query) use ($authId, $user) {
                $query->where('sender_id', $user->id)->where('receiver_id', $authId);
            })
            ->whereNotNull('message')
            ->where('message', 'ilike', '%'.$data['q'].'%')
            ->with(['sender', 'receiver'])
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        return MessageResource::collection($messages);
    }

    public function downloadAttachment(Message $message)
    {
        $user = request()->user();
        if ($message->sender_id !== $user->id && $message->receiver_id !== $user->id && ! $user->hasAnyRole(['admin', 'super-admin'])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $dl = app(MessageService::class)->prepareDownload($message);

        return \Illuminate\Support\Facades\Storage::disk('public')->download($dl['path'], $dl['name'], $dl['headers']);
    }

    public function typing(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'is_typing' => ['required', 'boolean'],
        ]);

        $user = $request->user();
        $targetUser = User::find($data['user_id']);

        $this->authorize('communicate', $targetUser);

        // Broadcast typing event
        broadcast(new \App\Events\UserTyping([
            'user_id' => $user->id,
            'target_user_id' => $data['user_id'],
            'is_typing' => $data['is_typing'],
            'user_name' => $user->name,
        ]));

        return response()->json(['status' => 'sent']);
    }

    public function onlineUsers(Request $request)
    {
        $user = $request->user();
        
        // Get users who have been active in the last 5 minutes
        $onlineUsers = User::where('last_seen_at', '>=', now()->subMinutes(5))
            ->where('id', '!=', $user->id)
            ->select(['id', 'name', 'email', 'last_seen_at'])
            ->get();

        return response()->json(['data' => $onlineUsers]);
    }

    public function react(Request $request, Message $message)
    {
        $data = $request->validate([
            'emoji' => ['required', 'string', 'max:10'],
        ]);

        $user = $request->user();
        
        // Check if user can see this message
        abort_unless(
            $message->sender_id === $user->id || $message->receiver_id === $user->id,
            403,
            'You cannot react to this message'
        );

        $reaction = app(MessageService::class)->addReaction($message, $user, $data['emoji']);

        return response()->json(['data' => $reaction]);
    }

    public function removeReaction(Request $request, Message $message)
    {
        $data = $request->validate([
            'emoji' => ['required', 'string', 'max:10'],
        ]);

        $user = $request->user();
        
        abort_unless(
            $message->sender_id === $user->id || $message->receiver_id === $user->id,
            403,
            'You cannot remove reaction from this message'
        );

        app(MessageService::class)->removeReaction($message, $user, $data['emoji']);

        return response()->noContent();
    }

    public function forward(Request $request, Message $message)
    {
        $data = $request->validate([
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $user = $request->user();
        
        abort_unless(
            $message->sender_id === $user->id || $message->receiver_id === $user->id,
            403,
            'You cannot forward this message'
        );

        $forwardedMessages = app(MessageService::class)->forwardMessage($message, $data['user_ids']);

        return response()->json(['data' => MessageResource::collection($forwardedMessages)]);
    }

    public function update(Request $request, Message $message)
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $user = $request->user();
        
        abort_unless(
            $message->sender_id === $user->id,
            403,
            'You can only edit your own messages'
        );

        $updatedMessage = app(MessageService::class)->editMessage($message, $data['message']);

        return new MessageResource($updatedMessage->load(['sender', 'receiver']));
    }

    public function reply(Request $request, Message $message)
    {
        $data = $request->validate([
            'message' => ['nullable', 'string', 'max:2000'],
            'attachment' => ['nullable', 'file', 'max:10240'], // 10MB
        ]);

        $user = $request->user();
        
        abort_unless(
            $message->sender_id === $user->id || $message->receiver_id === $user->id,
            403,
            'You cannot reply to this message'
        );

        $replyData = [
            'message' => $data['message'] ?? null,
            'attachment' => $request->file('attachment'),
            'reply_to_id' => $message->id,
        ];

        $targetUser = $message->sender_id === $user->id ? $message->receiver : $message->sender;
        $replyMessage = app(MessageService::class)->sendDirectMessage($targetUser, $replyData);

        return new MessageResource($replyMessage->load(['sender', 'receiver', 'replyTo']));
    }

    public function sendVoiceMessage(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'voice_message' => ['required', 'file', 'mimes:mp3,wav,m4a,ogg', 'max:5120'], // 5MB
            'duration' => ['required', 'integer', 'min:1', 'max:300'], // max 5 minutes
        ]);

        $user = $request->user();
        $targetUser = User::find($data['user_id']);

        $this->authorize('communicate', $targetUser);

        $messageData = [
            'voice_message' => $request->file('voice_message'),
            'duration' => $data['duration'],
        ];

        $message = app(MessageService::class)->sendDirectMessage($targetUser, $messageData);

        return new MessageResource($message->load(['sender', 'receiver']));
    }

    public function getMedia(Request $request, User $user)
    {
        $this->authorize('communicate', $user);
        $authId = $request->user()->id;

        $mediaMessages = Message::query()
            ->where(function ($q) use ($authId, $user) {
                $q->where('sender_id', $authId)->where('receiver_id', $user->id);
            })
            ->orWhere(function ($q) use ($authId, $user) {
                $q->where('sender_id', $user->id)->where('receiver_id', $authId);
            })
            ->whereNotNull('attachment')
            ->whereIn('attachment_type', ['image', 'video'])
            ->with(['sender', 'receiver'])
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 20));

        return MessageResource::collection($mediaMessages);
    }

    public function getFiles(Request $request, User $user)
    {
        $this->authorize('communicate', $user);
        $authId = $request->user()->id;

        $fileMessages = Message::query()
            ->where(function ($q) use ($authId, $user) {
                $q->where('sender_id', $authId)->where('receiver_id', $user->id);
            })
            ->orWhere(function ($q) use ($authId, $user) {
                $q->where('sender_id', $user->id)->where('receiver_id', $authId);
            })
            ->whereNotNull('attachment')
            ->whereNotIn('attachment_type', ['image', 'video'])
            ->with(['sender', 'receiver'])
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 20));

        return MessageResource::collection($fileMessages);
    }

    public function clearThread(Request $request, User $user)
    {
        $this->authorize('communicate', $user);
        $authId = $request->user()->id;

        $deletedCount = Message::query()
            ->where(function ($q) use ($authId, $user) {
                $q->where('sender_id', $authId)->where('receiver_id', $user->id);
            })
            ->orWhere(function ($q) use ($authId, $user) {
                $q->where('sender_id', $user->id)->where('receiver_id', $authId);
            })
            ->delete();

        return response()->json(['message' => 'Thread cleared', 'deleted_count' => $deletedCount]);
    }

    public function markAsRead(Request $request, Message $message)
    {
        $user = $request->user();
        
        abort_unless(
            $message->receiver_id === $user->id,
            403,
            'You can only mark messages sent to you as read'
        );

        if (!$message->read_at) {
            $message->update(['read_at' => now()]);

            // Broadcast read receipt
            broadcast(new \App\Events\MessageRead([
                'message_id' => $message->id,
                'user_id' => $user->id,
                'read_at' => $message->read_at,
            ]));
        }

        return response()->json(['status' => 'marked_as_read']);
    }
}
