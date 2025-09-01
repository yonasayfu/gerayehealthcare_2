<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;
use App\Models\GroupMessage;

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
                new PrivateChannel('users.' . $this->message->receiver_id),
            ];
        } elseif ($this->message instanceof GroupMessage) {
            return [
                new PrivateChannel('groups.' . $this->message->group_id),
            ];
        }

        return [];
    }

    public function broadcastWith()
    {
        return ['message' => $this->message->load('sender', 'reactions', 'replyTo')];
    }
}
