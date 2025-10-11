<?php

namespace App\Notifications;

use App\Models\EventParticipant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class EventParticipantAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public EventParticipant $participant) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $event = $this->participant->event;
        return [
            'title' => 'You are registered for an event',
            'message' => $event?->title ? ('Event: '.$event->title) : 'New event registration',
            'type' => 'event_participant_assigned',
            'event_id' => $this->participant->event_id,
            'participant_id' => $this->participant->id,
            'url' => route('admin.events.show', $this->participant->event_id),
        ];
    }

    public function toPush(object $notifiable): array
    {
        $payload = $this->toArray($notifiable);

        return [
            'title' => $payload['title'] ?? 'Event Registration',
            'body' => $payload['message'] ?? 'You have been registered for a new event.',
            'data' => $payload,
            'channel' => 'general',
        ];
    }
}
