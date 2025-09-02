<?php

namespace App\Http\Controllers;

use App\DTOs\CreateMessageDTO;
use App\Events\MessageReacted;
use App\Models\Message;
use App\Models\Reaction;
use App\Models\User;
use App\Notifications\NewMessageReceived;
use App\Services\MessageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class MessageController extends OptimizedBaseController
{
    protected MessageService $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
        $this->middleware('auth');
        $this->middleware('can:view-messages')->only(['index', 'show', 'getData']);
        $this->middleware('can:send-messages')->only(['store']);
        $this->middleware('can:delete-messages')->only(['destroy']);
    }

    /**
     * Display messages interface
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get conversation list
        $conversations = $this->messageService->getConversations($user->id, $request->get('search'));

        // Get selected conversation if specified
        $selectedConversation = null;
        $messages = [];

        if ($request->has('recipient')) {
            $recipient = User::findOrFail($request->get('recipient'));
            $selectedConversation = $recipient;
            $messages = $this->messageService->getConversationMessages($user->id, $recipient->id);
        }

        return Inertia::render('Messages/Index', [
            'conversations' => $conversations,
            'selectedConversation' => $selectedConversation,
            'messages' => $messages,
            'unreadCount' => $this->messageService->getUnreadCount($user->id),
        ]);
    }

    /**
     * Get conversation data (AJAX endpoint)
     */
    public function getData(Request $request, ?User $recipient = null)
    {
        $user = Auth::user();
        $messages = [];
        $selectedConversationUser = null;

        // Get conversations with search
        $conversationsQuery = User::where('id', '!=', $user->id);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $conversationsQuery->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $conversations = $conversationsQuery->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'profile_photo_path' => $user->profile_photo_path,
                'last_message' => $this->messageService->getLastMessage(Auth::id(), $user->id),
                'unread_count' => $this->messageService->getUnreadCount(Auth::id(), $user->id),
            ];
        });

        // Get messages for selected conversation
        if ($recipient) {
            $selectedConversationUser = $recipient;
            $messages = $this->messageService->getConversationMessages($user->id, $recipient->id);

            // Mark messages as read
            $this->messageService->markConversationAsRead($user->id, $recipient->id);
        }

        return response()->json([
            'conversations' => $conversations,
            'selectedConversationUser' => $selectedConversationUser,
            'messages' => $messages,
            'unreadCount' => $this->messageService->getUnreadCount($user->id),
        ]);
    }

    /**
     * Store a new message
     */
    public function store(Request $request)
    {
        try {
            $dto = CreateMessageDTO::fromRequest($request);
            $message = $this->messageService->create($dto);

            // Send notification to receiver
            $receiver = User::find($message->receiver_id);
            if ($receiver) {
                $receiver->notify(new NewMessageReceived($message));
            }

            if ($request->wantsJson()) {
                return $this->success($message->load(['sender', 'receiver']), 'Message sent successfully', 201);
            }

            return redirect()->back()->with('success', 'Message sent successfully');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to send message');
        }
    }

    /**
     * Display a specific message
     */
    public function show(Request $request, Message $message)
    {
        try {
            // Check authorization
            if (! $this->messageService->canViewMessage($message, Auth::id())) {
                return $this->error('Unauthorized to view this message', 403);
            }

            // Mark as read if user is receiver
            if ($message->receiver_id === Auth::id()) {
                $message->markAsRead();
            }

            if ($request->wantsJson()) {
                return $this->success($message->load(['sender', 'receiver']), 'Message retrieved successfully');
            }

            return Inertia::render('Messages/Show', [
                'message' => $message->load(['sender', 'receiver']),
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to retrieve message');
        }
    }

    /**
     * Delete a message
     */
    public function destroy(Request $request, Message $message)
    {
        try {
            if (! $this->messageService->canDeleteMessage($message, Auth::id())) {
                return $this->error('Unauthorized to delete this message', 403);
            }

            $this->messageService->delete($message->id);

            if ($request->wantsJson()) {
                return $this->success(null, 'Message deleted successfully');
            }

            return redirect()->back()->with('success', 'Message deleted successfully');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to delete message');
        }
    }

    /**
     * Mark message as read
     */
    public function markAsRead(Request $request, Message $message)
    {
        try {
            if ($message->receiver_id !== Auth::id()) {
                return $this->error('Unauthorized to mark this message as read', 403);
            }

            $message->markAsRead();

            return $this->success(null, 'Message marked as read');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to mark message as read');
        }
    }

    /**
     * Mark message as unread
     */
    public function markAsUnread(Request $request, Message $message)
    {
        try {
            if ($message->receiver_id !== Auth::id()) {
                return $this->error('Unauthorized to mark this message as unread', 403);
            }

            $message->markAsUnread();

            return $this->success(null, 'Message marked as unread');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to mark message as unread');
        }
    }

    /**
     * Get unread messages count
     */
    public function getUnreadCount(Request $request)
    {
        $count = $this->messageService->getUnreadCount(Auth::id());

        return $this->success(['count' => $count], 'Unread count retrieved successfully');
    }

    /**
     * React to a message with an emoji
     */
    public function react(Request $request, Message $message)
    {
        try {
            // Ensure the authenticated user is part of the conversation
            $user = Auth::user();
            if ($message->sender_id !== $user->id && $message->receiver_id !== $user->id) {
                return $this->error('Unauthorized to react to this message', 403);
            }

            $validated = $request->validate([
                'emoji' => ['required', 'string', 'max:16'],
            ]);

            $reaction = Reaction::firstOrCreate([
                'reactable_type' => Message::class,
                'reactable_id' => $message->id,
                'user_id' => $user->id,
                'emoji' => $validated['emoji'],
            ]);

            broadcast(new MessageReacted($reaction));

            return $this->success($reaction->load('user'), 'Reaction added successfully');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to add reaction');
        }
    }

    /**
     * Remove a reaction from a message
     */
    public function removeReaction(Request $request, Message $message)
    {
        try {
            $user = Auth::user();
            if ($message->sender_id !== $user->id && $message->receiver_id !== $user->id) {
                return $this->error('Unauthorized to remove reaction from this message', 403);
            }

            $validated = $request->validate([
                'emoji' => ['required', 'string', 'max:16'],
            ]);

            $reaction = Reaction::where([
                'reactable_type' => Message::class,
                'reactable_id' => $message->id,
                'user_id' => $user->id,
                'emoji' => $validated['emoji'],
            ])->first();

            if ($reaction) {
                $reaction->delete();

                return $this->success(null, 'Reaction removed successfully');
            }

            return $this->error('Reaction not found', 404);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to remove reaction');
        }
    }

    /**
     * Indicate the current user is typing to a receiver
     */
    public function typing(Request $request)
    {
        try {
            $validated = $request->validate([
                'receiver_id' => ['required', 'integer', 'exists:users,id'],
            ]);

            $receiver = User::findOrFail($validated['receiver_id']);

            $key = sprintf('typing:%d:%d', Auth::id(), $receiver->id);
            Cache::put($key, true, now()->addSeconds(5));

            return $this->success(null, 'Typing status updated');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to update typing status');
        }
    }

    /**
     * Check if the given user is typing to the authenticated user
     */
    public function typingStatus(Request $request, User $user)
    {
        try {
            $key = sprintf('typing:%d:%d', $user->id, Auth::id());
            $isTyping = Cache::has($key);

            return $this->success(['typing' => $isTyping], 'Typing status retrieved');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to get typing status');
        }
    }

    /**
     * Export conversation thread as CSV
     */
    public function exportThreadCsv(Request $request, User $user)
    {
        try {
            $authId = Auth::id();

            $filename = 'chat_'.$authId.'_'.$user->id.'_'.now()->format('Ymd_His').'.csv';
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function () use ($authId, $user) {
                $out = fopen('php://output', 'w');

                // Add UTF-8 BOM for proper encoding
                fwrite($out, "\xEF\xBB\xBF");

                // Add CSV headers
                fputcsv($out, ['Time', 'Sender', 'Receiver', 'Message', 'Has Attachment']);

                // Get messages between users
                $messages = Message::where(function ($q) use ($authId, $user) {
                    $q->where('sender_id', $authId)->where('receiver_id', $user->id);
                })
                    ->orWhere(function ($q) use ($authId, $user) {
                        $q->where('sender_id', $user->id)->where('receiver_id', $authId);
                    })
                    ->with(['sender', 'receiver'])
                    ->orderBy('created_at')
                    ->get();

                foreach ($messages as $message) {
                    fputcsv($out, [
                        optional($message->created_at)->toDateTimeString(),
                        optional($message->sender)->name ?? 'Unknown',
                        optional($message->receiver)->name ?? 'Unknown',
                        $message->message ?? '[No text content]',
                        $message->hasAttachment() ? 'Yes' : 'No',
                    ]);
                }

                fclose($out);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to export conversation');
        }
    }
}
