<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MessagePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any messages.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view their messages
    }

    /**
     * Determine whether the user can view the message.
     */
    public function view(User $user, Message $message): bool
    {
        // User can view messages they sent or received
        return $user->id === $message->sender_id || $user->id === $message->receiver_id;
    }

    /**
     * Determine whether the user can create messages.
     */
    public function create(User $user): bool
    {
        return true; // All authenticated users can send messages
    }

    /**
     * Determine whether the user can update the message.
     */
    public function update(User $user, Message $message): bool
    {
        // Only the sender can update their own messages
        return $user->id === $message->sender_id;
    }

    /**
     * Determine whether the user can delete the message.
     */
    public function delete(User $user, Message $message): bool
    {
        // Only the sender can delete their own messages
        return $user->id === $message->sender_id;
    }

    /**
     * Determine whether the user can restore the message.
     */
    public function restore(User $user, Message $message): bool
    {
        // Only the sender can restore their own messages
        return $user->id === $message->sender_id;
    }

    /**
     * Determine whether the user can permanently delete the message.
     */
    public function forceDelete(User $user, Message $message): bool
    {
        // Only the sender can permanently delete their own messages
        return $user->id === $message->sender_id;
    }

    /**
     * Determine whether the user can mark the message as read.
     */
    public function markAsRead(User $user, Message $message): bool
    {
        // Only the receiver can mark a message as read
        return $user->id === $message->receiver_id;
    }

    /**
     * Determine whether the user can mark the message as unread.
     */
    public function markAsUnread(User $user, Message $message): bool
    {
        // Only the receiver can mark a message as unread
        return $user->id === $message->receiver_id;
    }

    /**
     * Determine whether the user can react to the message.
     */
    public function react(User $user, Message $message): bool
    {
        // Users can react to messages they are part of (sender or receiver)
        return $user->id === $message->sender_id || $user->id === $message->receiver_id;
    }

    /**
     * Determine whether the user can communicate with another user.
     */
    public function communicate(User $authUser, User $otherUser): bool
    {
        // Users cannot message themselves
        if ($authUser->id === $otherUser->id) {
            return false;
        }

        // Check if both users have staff relationships
        $authIsStaff = $authUser->staff !== null;
        $otherIsStaff = $otherUser->staff !== null;

        // Staff can communicate with other staff
        if ($authIsStaff && $otherIsStaff) {
            return true;
        }

        // For now, allow all authenticated users to communicate
        // This can be customized based on specific business rules
        return true;
    }

    /**
     * Determine whether the user can export conversation.
     */
    public function exportConversation(User $user, User $otherUser): bool
    {
        // Users can export conversations they are part of
        return $this->communicate($user, $otherUser);
    }

    /**
     * Determine whether the user can view typing status.
     */
    public function viewTypingStatus(User $user, User $otherUser): bool
    {
        // Users can view typing status if they can communicate
        return $this->communicate($user, $otherUser);
    }

    /**
     * Determine whether the user can send typing indicator.
     */
    public function sendTypingIndicator(User $user, User $otherUser): bool
    {
        // Users can send typing indicators if they can communicate
        return $this->communicate($user, $otherUser);
    }
}
