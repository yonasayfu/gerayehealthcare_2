<?php

namespace App\Listeners;

use App\Events\CaregiverAssigned;

class SendCaregiverAssignmentNotification
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
    public function handle(CaregiverAssigned $event): void
    {
        // For now, we'll just log the information.
        // Later, this can be replaced with actual notification logic (e.g., email, SMS).
        \Illuminate\Support\Facades\Log::info("Caregiver assigned: Staff #{$event->assignment->staff_id} to Patient #{$event->assignment->patient_id}");
    }
}
