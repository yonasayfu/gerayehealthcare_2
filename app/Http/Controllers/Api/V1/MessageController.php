<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\SendMessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Models\User;
use App\Services\Messaging\MessageService;
use Illuminate\Http\Request;

class MessageController extends BaseApiController
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

    public function downloadAttachment(Message $message)
    {
        $user = request()->user();
        if ($message->sender_id !== $user->id && $message->receiver_id !== $user->id && ! $user->hasAnyRole(['admin', 'super-admin'])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $dl = app(MessageService::class)->prepareDownload($message);

        return \Illuminate\Support\Facades\Storage::disk('public')->download($dl['path'], $dl['name'], $dl['headers']);
    }
}
