<?php

namespace App\Listeners;

use App\Events\StaffAssignedToEvent;
use App\Models\EventStaffAssignment;
use Illuminate\Support\Facades\Log;

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
