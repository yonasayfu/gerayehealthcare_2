<?php

namespace App\Listeners;

use App\Events\PatientCreatedFromRecommendation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreatePatientFromRecommendation
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
    public function handle(PatientCreatedFromRecommendation $event): void
    {
        $patient = Patient::firstOrCreate(
            ['phone_number' => $event->patientPhone],
            [
                'full_name' => $event->patientName,
                'email' => null,
                'date_of_birth' => null,
                'gender' => null,
                'address' => null,
                'emergency_contact' => null,
                'source' => 'Event Recommendation',
                'geolocation' => null,
                'patient_code' => 'PAT-' . str_pad(Patient::count() + 1, 5, '0', STR_PAD_LEFT),
            ]
        );

        $event->eventRecommendation->linked_patient_id = $patient->id;
        $event->eventRecommendation->save();
    }
}
