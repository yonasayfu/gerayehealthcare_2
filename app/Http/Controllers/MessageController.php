<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Http\Requests\StoreMessageRequest;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Message;
use App\Models\User;
use App\Notifications\NewMessageReceived;
use App\Services\Messaging\GroupMessageService;
use App\Services\Messaging\MessageService;
use App\Services\Messaging\TelegramInboxService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class MessageController extends Controller
{
    /**
     * Render the messaging inbox via Inertia.
     */
    public function index(Request $request, ?User $recipient = null): Response
    {
        $conversationId = $request->input('conversation_id');
        $context = $request->input('context');

        if ($recipient) {
            $conversationId = $recipient->id;
            $context = \App\Services\Messaging\TelegramInboxService::CONTEXT_DIRECT;
        }

        $payload = $this->inboxService()->buildPayload($request->user(), [
            'context' => $context,
            'conversation_id' => $conversationId,
            'search' => $request->input('search', ''),
        ]);

        return \Inertia\Inertia::render('Admin/Messages/Index', [
            'initialSections' => $payload['sections'],
            'initialSelectedConversation' => $payload['selectedConversation'],
            'initialMessages' => $payload['messages'],
            'initialSearch' => $payload['search'],
            'initialContext' => $payload['context'],
            'messageRoutes' => [
                'index' => 'messages.inbox',
                'data' => 'messages.data',
                'store' => 'messages.store',
                'destroy' => 'messages.destroy',
                'download' => 'messages.download',
                'markRead' => 'messages.markRead',
                'markUnread' => 'messages.markUnread',
            'update' => 'messages.update',
            'typing' => 'messages.typing',
            'typingStatus' => 'messages.typingStatus',
            'pin' => 'messages.pin',
            'unpin' => 'messages.unpin',
            'hide' => 'messages.hide',
            'bulkDestroy' => 'messages.bulkDestroy',
            'search' => 'messages.thread.search',
            'groupStore' => 'groups.messages.store',
            'groupDestroy' => 'groups.messages.destroy',
            'groupDownload' => 'groups.messages.attachment',
            'groupUpdate' => 'groups.messages.update',
            'groupPin' => 'groups.messages.pin',
            'groupUnpin' => 'groups.messages.unpin',
            'groupSearch' => 'groups.messages.search',
        ],
        'canModerate' => $request->user()->hasAnyRole([
            \App\Enums\RoleEnum::SUPER_ADMIN->value,
            \App\Enums\RoleEnum::ADMIN->value,
        ]) || $request->user()->can('delete all messages'),
        ]);
    }

    /**
     * Fetch conversation data as JSON (used by the SPA for async refresh).
     */
    public function getData(Request $request, ?User $recipient = null)
    {
        try {
            $conversationId = $request->input('conversation_id');
            $context = $request->input('context');

            if ($recipient) {
                $conversationId = $recipient->id;
                $context = TelegramInboxService::CONTEXT_DIRECT;
            }

            $payload = $this->inboxService()->buildPayload($request->user(), [
                'context' => $context,
                'conversation_id' => $conversationId,
                'search' => $request->input('search', ''),
            ]);

            return response()->json($payload);
        } catch (\Throwable $e) {
            Log::error('Chat getData failed', [
                'message' => $e->getMessage(),
                'trace' => substr($e->getTraceAsString(), 0, 2000),
            ]);

            return response()->json([
                'sections' => [],
                'selectedConversation' => null,
                'messages' => [],
                'search' => $request->input('search', ''),
                'context' => $request->input('context', TelegramInboxService::CONTEXT_DIRECT),
            ], 500);
        }
    }

    /**
     * Store a new message in the database.
     */
    public function store(StoreMessageRequest $request)
    {
        // Debug authentication
        if (! Auth::check()) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $validated = $request->validated();
        $context = $validated['context'] ?? TelegramInboxService::CONTEXT_DIRECT;
        $service = $this->inboxService();

        if ($context === TelegramInboxService::CONTEXT_CHANNEL) {
            $group = Group::findOrFail($validated['group_id']);
            $this->ensureGroupMember($group, Auth::id());

            $message = app(GroupMessageService::class)->storeMessage($group, [
                'message' => $validated['message'] ?? null,
                'attachment' => $request->file('attachment'),
                'reply_to_id' => $validated['reply_to_id'] ?? null,
                'priority' => $validated['priority'] ?? 'normal',
                'message_type' => $validated['message_type'] ?? ($request->file('attachment') ? 'file' : 'text'),
            ]);

            $message->load(['sender', 'reactions', 'replyTo']);

            broadcast(new \App\Events\NewMessage($message));

            return response()->json([
                'message' => $service->transformChannelMessage($message, Auth::id()),
                'conversation' => $service->transformChannelConversation(
                    $group->loadCount('members'),
                    $service->isOrganizationChannel($group),
                    optional($message->created_at)?->toIso8601String()
                ),
            ], 201);
        }

        $recipient = User::findOrFail($validated['receiver_id']);
        $this->authorize('communicate', $recipient);

        $message = app(MessageService::class)->sendDirectMessage($recipient, [
            'message' => $validated['message'] ?? null,
            'attachment' => $request->file('attachment'),
        ]);

        $recipient->notify(new NewMessageReceived($message->load('sender')));

        broadcast(new \App\Events\NewMessage($message));

        $message->load(['sender', 'receiver', 'messageReactions', 'replyTo']);

        return response()->json([
            'message' => $service->transformDirectMessage($message, Auth::id()),
            'conversation' => $service->transformDirectConversation(
                $recipient->fresh(['staff']),
                0,
                optional($message->created_at)?->toIso8601String()
            ),
        ], 201);
    }

    /**
     * Delete a message.
     */
    public function destroy(Message $message)
    {
        $user = Auth::user();

        // Check if user can delete this message
        $canDelete = $message->sender_id === $user->id
            || $user->hasRole(RoleEnum::SUPER_ADMIN->value)
            || $user->hasRole(RoleEnum::ADMIN->value)
            || $user->can('delete all messages');

        if (! $canDelete) {
            return response()->json(['error' => 'Unauthorized to delete this message.'], 403);
        }

        $hardDeleted = false;
        try {
            app(MessageService::class)->deleteMessage($message);
            $hardDeleted = true;
        } catch (\Throwable $e) {
            // Fallback: hide for both sides so neither party sees it
            $message->forceFill([
                'deleted_for_sender_at' => now(),
                'deleted_for_receiver_at' => now(),
            ])->save();
        }

        // Broadcast the deletion event to update UIs real-time
        broadcast(new \App\Events\MessageDeleted($message));

        return response()->noContent();
    }

    /**
     * Hide a message for the current user only (soft delete per-user).
     */
    public function hide(Message $message)
    {
        $user = Auth::user();

        abort_unless($message->sender_id === $user->id || $message->receiver_id === $user->id, 403);

        if ($message->sender_id === $user->id) {
            $message->forceFill(['deleted_for_sender_at' => now()])->save();
        }

        if ($message->receiver_id === $user->id) {
            $message->forceFill(['deleted_for_receiver_at' => now()])->save();
        }

        return response()->noContent();
    }

    /**
     * Edit message text (sender or admins).
     */
    public function update(Request $request, Message $message)
    {
        $user = Auth::user();

        // Check if user can edit this message
        $canEdit = $message->sender_id === $user->id
            || $user->hasRole(RoleEnum::SUPER_ADMIN->value)
            || $user->hasRole(RoleEnum::ADMIN->value)
            || $user->can('edit all messages');

        if (! $canEdit) {
            return response()->json(['error' => 'Unauthorized to edit this message.'], 403);
        }

        $data = $request->validate(['message' => ['required', 'string', 'max:5000']]);
        $message->update(['message' => $data['message']]);

        $message->load(['sender', 'receiver', 'messageReactions', 'replyTo']);

        broadcast(new \App\Events\MessageUpdated($message));

        return response()->json([
            'message' => $this->inboxService()->transformDirectMessage($message, $user->id),
        ]);
    }

    /**
     * Secure download for direct message attachments (only sender, receiver, or admin).
     */
    public function downloadAttachment(Message $message)
    {
        $user = Auth::user();
        abort_unless(
            $message->sender_id === $user->id
            || $message->receiver_id === $user->id
            || $user->hasRole(RoleEnum::SUPER_ADMIN->value)
            || $user->hasRole(RoleEnum::ADMIN->value),
            403
        );

        if (! $message->attachment_path) {
            return response()->json(['error' => 'Attachment not found.'], 404);
        }
        $dl = app(MessageService::class)->prepareDownload($message);

        return \Illuminate\Support\Facades\Storage::disk('public')->download($dl['path'], $dl['name'], $dl['headers']);
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

        return response()->json([
            'message' => $this->inboxService()->transformDirectMessage(
                $message->fresh(['sender', 'receiver', 'messageReactions', 'replyTo']),
                Auth::id()
            ),
        ]);
    }

    public function pin(Message $message)
    {
        $user = Auth::user();

        abort_unless($message->sender_id === $user->id
            || $message->receiver_id === $user->id
            || $user->hasRole(RoleEnum::SUPER_ADMIN->value)
            || $user->hasRole(RoleEnum::ADMIN->value)
            || $user->can('edit all messages'), 403);

        $message = app(MessageService::class)->pinMessage($message)->load(['sender', 'receiver', 'messageReactions', 'replyTo']);

        broadcast(new \App\Events\MessageUpdated($message));

        return response()->json([
            'message' => $this->inboxService()->transformDirectMessage($message, $user->id),
        ]);
    }

    public function unpin(Message $message)
    {
        $user = Auth::user();

        abort_unless($message->sender_id === $user->id
            || $message->receiver_id === $user->id
            || $user->hasRole(RoleEnum::SUPER_ADMIN->value)
            || $user->hasRole(RoleEnum::ADMIN->value)
            || $user->can('edit all messages'), 403);

        $message = app(MessageService::class)->unpinMessage($message)->load(['sender', 'receiver', 'messageReactions', 'replyTo']);

        broadcast(new \App\Events\MessageUpdated($message));

        return response()->json([
            'message' => $this->inboxService()->transformDirectMessage($message, $user->id),
        ]);
    }

    public function bulkDestroy(Request $request)
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:messages,id'],
        ]);

        $user = Auth::user();
        $service = app(MessageService::class);

        $messages = Message::whereIn('id', $data['ids'])->get();

        foreach ($messages as $message) {
            $canDelete = $message->sender_id === $user->id
                || $message->receiver_id === $user->id
                || $user->hasRole(RoleEnum::SUPER_ADMIN->value)
                || $user->hasRole(RoleEnum::ADMIN->value)
                || $user->can('delete all messages');

            if (! $canDelete) {
                return response()->json(['error' => 'Unauthorized to delete one or more messages.'], 403);
            }

            $service->deleteMessage($message);
            broadcast(new \App\Events\MessageDeleted($message));
        }

        return response()->noContent();
    }

    public function searchThread(Request $request, User $user)
    {
        $this->authorize('communicate', $user);

        $data = $request->validate([
            'q' => ['required', 'string', 'max:255'],
        ]);

        $authId = Auth::id();
        $messages = Message::query()
            ->where(function ($query) use ($authId, $user) {
                $query->where('sender_id', $authId)->where('receiver_id', $user->id);
            })
            ->orWhere(function ($query) use ($authId, $user) {
                $query->where('sender_id', $user->id)->where('receiver_id', $authId);
            })
            ->whereNotNull('message')
            ->where('message', 'ilike', '%'.$data['q'].'%')
            ->with(['sender', 'receiver', 'messageReactions', 'replyTo'])
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        $service = $this->inboxService();

        return response()->json([
            'results' => $messages->map(fn (Message $message) => $service->transformDirectMessage($message, $authId))->values(),
        ]);
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

        return response()->json([
            'message' => $this->inboxService()->transformDirectMessage(
                $message->fresh(['sender', 'receiver', 'messageReactions', 'replyTo']),
                Auth::id()
            ),
        ]);
    }
    // Reactions functionality has been removed (endpoint retired)

    /**
     * Indicate the current user is typing to a receiver (short-lived flag).
     */
    // Typing indicators removed (endpoints retired)

    /**
     * Export a CSV of the conversation between the authenticated user and the given user.
     */
    public function exportThreadCsv(Request $request, User $user)
    {
        $this->authorize('communicate', $user);
        $authId = Auth::id();

        $filename = 'chat_'.$authId.'_'.$user->id.'_'.now()->format('Ymd_His').'.csv';
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

    protected function inboxService(): TelegramInboxService
    {
        return app(TelegramInboxService::class);
    }

    protected function ensureGroupMember(Group $group, int $userId): void
    {
        $isMember = GroupMember::where('group_id', $group->id)
            ->where('user_id', $userId)
            ->exists();

        abort_unless($isMember, 403, 'Unauthorized to interact with this channel.');
    }
}
