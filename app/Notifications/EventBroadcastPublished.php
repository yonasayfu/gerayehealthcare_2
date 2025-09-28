<?php

namespace App\Notifications;

use App\Models\EventBroadcast;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class EventBroadcastPublished extends Notification implements ShouldQueue
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
            'title' => 'New Event Update',
            'message' => ($event?->title ? ('Update for: '.$event->title) : 'Event update').' via '.$this->broadcast->channel,
            'type' => 'event_broadcast_published',
            'event_broadcast_id' => $this->broadcast->id,
            'event_id' => $this->broadcast->event_id,
            'channel' => $this->broadcast->channel,
            'url' => route('admin.events.show', $this->broadcast->event_id),
        ];
    }
}

