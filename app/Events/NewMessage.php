<?php

namespace App\Events;

use App\Models\GroupMessage;
use App\Models\Message;
use App\Services\Messaging\TelegramInboxService;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct($message)
    {
        $this->message = $message;
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
        $service = app(TelegramInboxService::class);

        if ($this->message instanceof Message) {
            // Direct messages use the dedicated MessageReaction model
            $message = $this->message->loadMissing(['sender.staff', 'receiver.staff', 'messageReactions', 'replyTo']);

            return [
                'message' => $service->transformDirectMessage($message, $message->receiver_id),
            ];
        }

        if ($this->message instanceof GroupMessage) {
            $message = $this->message->loadMissing(['sender.staff', 'reactions', 'replyTo']);

            return [
                'message' => $service->transformChannelMessage($message, 0),
            ];
        }

        return ['message' => null];
    }
}
