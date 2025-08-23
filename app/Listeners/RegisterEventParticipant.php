<?php

namespace App\Listeners;

use App\Events\EventParticipantRegistered;
use App\Models\EventParticipant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RegisterEventParticipant
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(EventParticipantRegistered $event): void
    {
        EventParticipant::firstOrCreate(
            [
                'event_id' => $event->eventId,
                'patient_id' => $event->patientId,
            ],
            ['status' => 'registered']
        );
    }
}
