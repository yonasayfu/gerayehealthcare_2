<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewMessageReceived extends Notification
{
    use Queueable;

    /**
     * The incoming message instance.
     */
    public Message $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database']; // We will store this notification in the database
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'sender_id' => $this->message->sender_id,
            'sender_name' => $this->message->sender->name,
            'message_preview' => $this->message->message, // Show full message
            'message_id' => $this->message->id,
            'conversation_id' => $this->message->sender_id, // The sender is the conversation partner
            'url' => null, // No URL for message notifications - handled by event
            'type' => 'new_message',
        ];
    }

    public function toPush(object $notifiable): array
    {
        $payload = $this->toArray($notifiable);

        return [
            'title' => 'New message from '.$payload['sender_name'],
            'body' => Str::limit($payload['message_preview'] ?? '', 120),
            'data' => $payload,
            'channel' => 'message',
        ];
    }
}
