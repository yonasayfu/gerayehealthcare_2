<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Reaction;
use App\Models\Message;
use App\Models\GroupMessage;

class MessageReacted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $reaction;

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
        } elseif ($reactable instanceof GroupMessage) {
            return [
                new PrivateChannel('groups.' . $reactable->group_id),
            ];
        }

        return [];
    }

    public function broadcastWith()
    {
        return ['reaction' => $this->reaction->load('user')];
    }
}
