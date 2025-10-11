<?php

namespace App\Notifications;

use App\Models\EventBroadcast;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class EventBroadcastCreated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public EventBroadcast $broadcast) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $event = $this->broadcast->event;
        return [
            'title' => 'New Event Broadcast Created',
            'message' => $event?->title ? ('Event: '.$event->title) : 'A broadcast has been created',
            'type' => 'event_broadcast_created',
            'event_broadcast_id' => $this->broadcast->id,
            'event_id' => $this->broadcast->event_id,
            'channel' => $this->broadcast->channel,
            'url' => route('admin.event-broadcasts.show', $this->broadcast->id),
        ];
    }

    public function toPush(object $notifiable): array
    {
        $payload = $this->toArray($notifiable);

        return [
            'title' => $payload['title'] ?? 'Event Broadcast',
            'body' => $payload['message'] ?? 'A new broadcast has been scheduled.',
            'data' => $payload,
            'channel' => 'general',
        ];
    }
}
