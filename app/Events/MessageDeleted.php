<?php

namespace App\Events;

use App\Models\GroupMessage;
use App\Models\Message;
use App\Services\Messaging\TelegramInboxService;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $messageId;

    public $conversationId;

    public $message; // Keep the message object to determine channel

    /**
     * Create a new event instance.
     */
    public function __construct($message)
    {
        $this->message = $message;
        $this->messageId = $message->id;

        if ($message instanceof Message) {
            $this->conversationId = $message->receiver_id;
        } elseif ($message instanceof GroupMessage) {
            $this->conversationId = $message->group_id;
        }
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        if ($this->message instanceof Message) {
            return [
                new PrivateChannel('users.'.$this->message->receiver_id),
                new PrivateChannel('users.'.$this->message->sender_id),
            ];
        } elseif ($this->message instanceof GroupMessage) {
            return [
                new PrivateChannel('groups.'.$this->message->group_id),
            ];
        }

        return [];
    }

    public function broadcastWith()
    {
        $context = null;

        if ($this->message instanceof Message) {
            $context = TelegramInboxService::CONTEXT_DIRECT;
        } elseif ($this->message instanceof GroupMessage) {
            $context = TelegramInboxService::CONTEXT_CHANNEL;
        }

        return [
            'messageId' => $this->messageId,
            'conversationId' => $this->conversationId,
            'context' => $context,
        ];
    }
}
