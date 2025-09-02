<?php

namespace App\Http\Controllers\Api\V1;

use App\DTOs\CreateMessageDTO;
use App\Http\Resources\MessageResource;
use App\Http\Resources\UserResource;
use App\Models\Message;
use App\Models\User;
use App\Services\MessageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends BaseApiController
{
    protected MessageService $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
        $this->middleware('auth:sanctum');
        $this->middleware('can:view-messages')->only(['threads', 'thread']);
        $this->middleware('can:send-messages')->only(['send']);
    }

    /**
     * Get conversation threads for authenticated user
     */
    public function threads(Request $request)
    {
        try {
            $user = Auth::user();
            $search = $request->get('search');

            $conversations = $this->messageService->getConversations($user->id, $search);

            return $this->success([
                'threads' => $conversations,
                'unread_count' => $this->messageService->getUnreadCount($user->id),
            ], 'Conversation threads retrieved successfully');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to retrieve conversation threads');
        }
    }

    /**
     * Get messages in a specific thread
     */
    public function thread(Request $request, User $user)
    {
        try {
            $authUser = Auth::user();
            $limit = $request->get('limit', 50);

            $messages = $this->messageService->getConversationMessages($authUser->id, $user->id, $limit);

            // Mark messages as read
            $this->messageService->markConversationAsRead($authUser->id, $user->id);

            return $this->success([
                'messages' => MessageResource::collection($messages),
                'conversation_with' => new UserResource($user),
                'total_messages' => $messages->count(),
            ], 'Thread messages retrieved successfully');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to retrieve thread messages');
        }
    }

    /**
     * Send a message in a thread
     */
    public function send(Request $request, User $user)
    {
        try {
            $request->merge(['receiver_id' => $user->id]);
            $dto = CreateMessageDTO::fromRequest($request);

            $message = $this->messageService->create($dto);

            // Send notification to receiver
            $user->notify(new \App\Notifications\NewMessageReceived($message));

            return $this->success(
                new MessageResource($message->load(['sender', 'receiver'])),
                'Message sent successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to send message');
        }
    }

    /**
     * Mark message as read
     */
    public function markRead(Request $request, Message $message)
    {
        try {
            if ($message->receiver_id !== Auth::id()) {
                return $this->error('Unauthorized action', 403);
            }

            $message->markAsRead();

            return $this->success(null, 'Message marked as read');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to mark message as read');
        }
    }

    /**
     * Delete a message
     */
    public function destroy(Request $request, Message $message)
    {
        try {
            if (! $this->messageService->canDeleteMessage($message, Auth::id())) {
                return $this->error('Unauthorized action', 403);
            }

            $this->messageService->delete($message->id);

            return $this->success(null, 'Message deleted successfully');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to delete message');
        }
    }

    /**
     * Get unread messages count
     */
    public function unreadCount(Request $request)
    {
        try {
            $count = $this->messageService->getUnreadCount(Auth::id());

            return $this->success(['count' => $count], 'Unread count retrieved successfully');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to get unread count');
        }
    }
}
