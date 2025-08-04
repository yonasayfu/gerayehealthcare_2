<?php

namespace App\Listeners;

use App\Events\StaffAssignedToEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AssignStaffToEvent
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
    public function handle(StaffAssignedToEvent $event): void
    {
        EventStaffAssignment::firstOrCreate(
            [
                'event_id' => $event->eventId,
                'staff_id' => $event->staffId,
            ],
            ['role' => 'Attended']
        );
    }
}
