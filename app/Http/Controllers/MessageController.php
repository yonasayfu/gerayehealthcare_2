<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Http\Requests\StoreMessageRequest;
use App\Models\Message;
use App\Models\User;
use App\Notifications\NewMessageReceived;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    /**
     * Fetches conversation list, selected conversation, and messages.
     * Handles initial selection if no recipient is specified.
     */
    public function getData(Request $request, ?User $recipient = null)
    {
        $user = Auth::user();
        $messages = [];
        $selectedConversationUser = null;

        try {
            // Limit conversation list to authorized counterparts, with admin override
            $base = User::where('id', '!=', $user->id);

            $conversationsQuery = (clone $base)->where(function ($q) use ($user) {
                if ($user->hasRole(RoleEnum::SUPER_ADMIN->value) || $user->hasRole(RoleEnum::ADMIN->value)) {
                    // Admins see all users
                    return; // No additional filtering for admins
                }

                // Staff can chat with all other users (simplified for better UX)
                if ($user->hasRole('staff') || $user->staff) {
                    return; // No additional filtering for staff - they can see all users
                }

                // For other roles, show all users
                return;
            });

            // Also include any counterparts I already have messages with
            $directIds = Message::query()
                ->selectRaw('CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END as uid', [$user->id])
                ->where(function ($q) use ($user) {
                    $q->where('sender_id', $user->id)->orWhere('receiver_id', $user->id);
                })
                ->pluck('uid')
                ->filter()
                ->unique()
                ->values();

            if ($directIds->isNotEmpty()) {
                $conversationsQuery = (clone $base)->where(function ($q) use ($conversationsQuery, $directIds) {
                    $q->whereIn('id', $conversationsQuery->pluck('id'))
                        ->orWhereIn('id', $directIds);
                });
            }

            if ($request->filled('search')) {
                $search = $request->input('search');
                $conversationsQuery->where(function ($sq) use ($search) {
                    $sq->where('name', 'ilike', "%{$search}%")
                        ->orWhere('email', 'ilike', "%{$search}%");
                });
            }

            $conversations = $conversationsQuery->orderBy('name', 'asc')->limit(100)->get();

            // Optimized unread counts with caching
            $cacheKey = "unread_counts_user_{$user->id}";
            $unreadCounts = Cache::remember($cacheKey, 300, function () use ($user) {
                return Message::selectRaw('sender_id, COUNT(*) as unread')
                    ->where('receiver_id', $user->id)
                    ->whereNull('read_at')
                    ->groupBy('sender_id')
                    ->pluck('unread', 'sender_id');
            });

            if ($recipient) {
                $selectedConversationUser = $recipient;
                // Ensure recipient appears in conversations for UI
                if (!$conversations->contains('id', $recipient->id)) {
                    $conversations->push($recipient);
                }
            } elseif ($conversations->isNotEmpty()) {
                $selectedConversationUser = $conversations->first();
            }

            if ($selectedConversationUser) {
                Message::where('sender_id', $selectedConversationUser->id)
                    ->where('receiver_id', $user->id)
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);

                $messages = Message::where(function ($query) use ($user, $selectedConversationUser) {
                    $query->where('sender_id', $user->id)
                        ->where('receiver_id', $selectedConversationUser->id);
                })->orWhere(function ($query) use ($user, $selectedConversationUser) {
                    $query->where('sender_id', $selectedConversationUser->id)
                        ->where('receiver_id', $user->id);
                })->with(['sender', 'receiver', 'reactions', 'replyTo'])
                    ->orderBy('created_at', 'asc')
                    ->get();
            }

            // Shape conversations with unread counts for UI
            $convData = $conversations->map(function ($u) use ($unreadCounts) {
                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'email' => $u->email,
                    'profile_photo_url' => $u->profile_photo_url ?? null,
                    'staff' => method_exists($u, 'staff') ? $u->staff : null,
                    'unread' => (int) ($unreadCounts[$u->id] ?? 0),
                ];
            });

            return response()->json([
                'conversations' => $convData,
                'selectedConversation' => $selectedConversationUser ? $selectedConversationUser->load('staff') : null,
                'messages' => $messages,
            ]);

        } catch (\Throwable $e) {
            Log::error('Chat getData failed', [
                'message' => $e->getMessage(),
                'trace' => substr($e->getTraceAsString(), 0, 2000),
            ]);

            // Fail-safe empty response instead of 500
            return response()->json([
                'conversations' => [],
                'selectedConversation' => null,
                'messages' => [],
            ]);
        }
    }

    /**
     * Store a new message in the database.
     */
    public function store(StoreMessageRequest $request)
    {
        // Debug authentication
        if (!Auth::check()) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $validated = $request->validated();

        $messageContent = $validated['message'] ?? null;
        $attachmentPath = null;
        $attachmentFilename = null;
        $attachmentMimeType = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $originalFilename = $file->getClientOriginalName();
            $mimeType = $file->getClientMimeType();

            $filePath = $file->store('messages/attachments', 'public');

            $attachmentPath = $filePath;
            $attachmentFilename = $originalFilename;
            $attachmentMimeType = $mimeType;
        }

        if (empty($messageContent) && empty($attachmentPath)) {
            return response()->json(['error' => 'Message content or an attachment is required.'], 422);
        }

        // Authorization: ensure sender may message receiver
        $recipient = User::findOrFail($validated['receiver_id']);
        $this->authorize('communicate', $recipient);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $validated['receiver_id'],
            'message' => $messageContent,
            'reply_to_id' => $validated['reply_to_id'] ?? null,
            'attachment_path' => $attachmentPath,
            'attachment_filename' => $attachmentFilename,
            'attachment_mime_type' => $attachmentMimeType,
        ]);

        // Dispatch the notification to the recipient (assuming NewMessageReceived notification is correctly set up)
        if ($recipient) {
            // Note: If you need to pass the newly created message to the notification,
            // you'd retrieve it before this point. For now, assuming basic notification.
            // Example: $recipient->notify(new NewMessageReceived(Message::latest()->first())); // Not ideal, but illustrative
            // Best practice is to pass the $message object from the create() call if needed.
            // But since the current notification only uses properties from the message, and not the object itself, it's fine.
            $recipient->notify(new NewMessageReceived($message->load('sender')));
        }

        broadcast(new \App\Events\NewMessage($message));

        // IMPORTANT CHANGE: Return no content for Inertia AJAX requests
        return response()->noContent();
    }

    /**
     * Delete a message.
     */
    public function destroy(Message $message)
    {
        $user = Auth::user();

        // Check if user can delete this message
        $canDelete = $message->sender_id === $user->id ||
        $user->hasRole(\App\Enums\RoleEnum::SUPER_ADMIN->value) ||
        $user->hasRole(\App\Enums\RoleEnum::ADMIN->value) ||
        $user->can('delete all messages');

        if (!$canDelete) {
            return response()->json(['error' => 'Unauthorized to delete this message.'], 403);
        }

        // Delete the attachment file from storage if exists
        if ($message->attachment_path) {
            Storage::disk('public')->delete($message->attachment_path);
        }

        // Broadcast the deletion event
        broadcast(new \App\Events\MessageDeleted($message));

        $message->delete();

        return response()->noContent();
    }

    /**
     * Edit message text (sender or admins).
     */
    public function update(Request $request, Message $message)
    {
        $user = Auth::user();

        // Check if user can edit this message
        $canEdit = $message->sender_id === $user->id ||
        $user->hasRole(\App\Enums\RoleEnum::SUPER_ADMIN->value) ||
        $user->hasRole(\App\Enums\RoleEnum::ADMIN->value) ||
        $user->can('edit all messages');

        if (!$canEdit) {
            return response()->json(['error' => 'Unauthorized to edit this message.'], 403);
        }

        $data = $request->validate(['message' => ['required', 'string', 'max:5000']]);
        $message->update(['message' => $data['message']]);

        broadcast(new \App\Events\MessageUpdated($message));

        return response()->json(['message' => 'Message updated successfully']);
    }

    /**
     * Download an attachment.
     */
    public function downloadAttachment(Message $message)
    {
        // Ensure the authenticated user is part of the conversation
        if ($message->sender_id !== Auth::id() && $message->receiver_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized to download this attachment.'], 403);
        }

        if (!$message->attachment_path || !Storage::disk('public')->exists($message->attachment_path)) {
            return response()->json(['error' => 'Attachment not found.'], 404);
        }

        return Storage::download(storage_path('app/public/' . $message->attachment_path), $message->attachment_filename);
    }

    /**
     * Mark a message as read.
     */
    public function markAsRead(Message $message)
    {
        // Only the receiver can mark a message as read
        if ($message->receiver_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized to mark this message as read.'], 403);
        }

        $message->update(['read_at' => now()]);

        return response()->noContent();
    }

    /**
     * Mark a message as unread.
     */
    public function markAsUnread(Message $message)
    {
        // Only the receiver can mark a message as unread
        if ($message->receiver_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized to mark this message as unread.'], 403);
        }

        $message->update(['read_at' => null]);

        return response()->noContent();
    }

    /**
     * React to a direct message (emoji per user).
     */
    public function react(Request $request, Message $message)
    {
        // Ensure the authenticated user is part of the conversation
        $user = Auth::user();
        if ($message->sender_id !== $user->id && $message->receiver_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized to react to this message.'], 403);
        }
        $data = $request->validate(['emoji' => ['required', 'string', 'max:16']]);
        $reaction = \App\Models\Reaction::firstOrCreate([
            'reactable_type' => Message::class,
            'reactable_id' => $message->id,
            'user_id' => $user->id,
            'emoji' => $data['emoji'],
        ]);

        broadcast(new \App\Events\MessageReacted($reaction));

        return response()->noContent();
    }

    /**
     * Indicate the current user is typing to a receiver (short-lived flag).
     */
    public function typing(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $receiver = User::findOrFail($validated['receiver_id']);

        // Simple authorization - users can indicate typing to any user they can message
        if (!Auth::user()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $key = sprintf('typing:%d:%d', Auth::id(), $receiver->id);
        Cache::put($key, true, now()->addSeconds(5));

        return response()->noContent();
    }

    /**
     * Check if the given user is typing to the authenticated user.
     */
    public function typingStatus(Request $request, User $user)
    {
        // Simple authorization - users can check typing status from any user
        if (!Auth::user()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $key = sprintf('typing:%d:%d', $user->id, Auth::id());

        return response()->json(['typing' => Cache::has($key)]);
    }

    /**
     * Export a CSV of the conversation between the authenticated user and the given user.
     */
    public function exportThreadCsv(Request $request, User $user)
    {
        $this->authorize('communicate', $user);
        $authId = Auth::id();

        $filename = 'chat_' . $authId . '_' . $user->id . '_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($authId, $user) {
            $out = fopen('php://output', 'w');
            fwrite($out, 'ï»¿'); // UTF-8 BOM
            fputcsv($out, ['Time', 'Sender', 'Receiver', 'Message']);

            $messages = Message::where(function ($q) use ($authId, $user) {
                $q->where('sender_id', $authId)->where('receiver_id', $user->id);
            })
                ->orWhere(function ($q) use ($authId, $user) {
                    $q->where('sender_id', $user->id)->where('receiver_id', $authId);
                })
                ->with(['sender', 'receiver'])
                ->orderBy('created_at')
                ->get();

            foreach ($messages as $m) {
                fputcsv($out, [
                    optional($m->created_at)->toDateTimeString(),
                    optional($m->sender)->name ?? $m->sender_id,
                    optional($m->receiver)->name ?? $m->receiver_id,
                    $m->message,
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
