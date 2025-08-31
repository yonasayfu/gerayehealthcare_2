<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Api\V1\SendMessageRequest;

class MessageController extends Controller
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
            ->where(function ($q) use ($userId) { $q->where('sender_id', $userId)->orWhere('receiver_id', $userId); })
            ->groupBy('uid')
            ->pluck('last_at', 'uid');

        $data = $counterparts->map(function ($u) use ($unreadCounts, $lastMessages) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'unread' => (int)($unreadCounts[$u->id] ?? 0),
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
        $validated = $request->validated();
        if (empty($validated['message']) && !$request->hasFile('attachment')) {
            return response()->json(['message' => 'Message text or attachment required'], 422);
        }

        $attachmentPath = null; $attachmentFilename = null; $attachmentMimeType = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentFilename = $file->getClientOriginalName();
            $attachmentMimeType = $file->getClientMimeType();
            $attachmentPath = $file->store('messages/attachments', 'public');
        }

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $user->id,
            'message' => $validated['message'] ?? null,
            'attachment_path' => $attachmentPath,
            'attachment_filename' => $attachmentFilename,
            'attachment_mime_type' => $attachmentMimeType,
        ]);

        return new MessageResource($message->load(['sender', 'receiver']));
    }
}
