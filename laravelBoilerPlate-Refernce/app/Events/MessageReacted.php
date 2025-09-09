<?php

namespace App\Events;

use App\Models\Reaction;
use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageReacted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Reaction $reaction;

    /**
     * Create a new event instance.
     */
    public function __construct(Reaction $reaction)
    {
        $this->reaction = $reaction;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $reactable = $this->reaction->reactable;

        if ($reactable instanceof Message) {
            return [
                new PrivateChannel('users.' . $reactable->receiver_id),
                new PrivateChannel('users.' . $reactable->sender_id),
            ];
        }

        return [];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'reaction' => $this->reaction->load('user'),
            'message_id' => $this->reaction->reactable_id,
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'message.reacted';
    }
}
