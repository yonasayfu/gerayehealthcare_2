<?php

namespace App\Providers;

use App\Events\CaregiverAssigned;
use App\Events\EventParticipantRegistered;
use App\Events\InventoryRequestSaved;
use App\Events\PatientCreatedFromRecommendation;
use App\Listeners\CheckForLowStock;
use App\Listeners\CreatePatientFromRecommendation;
use App\Listeners\RegisterEventParticipant;
use App\Listeners\SendCaregiverAssignmentNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        CaregiverAssigned::class => [
            SendCaregiverAssignmentNotification::class,
        ],
        InventoryRequestSaved::class => [
            CheckForLowStock::class,
        ],
        PatientCreatedFromRecommendation::class => [
            CreatePatientFromRecommendation::class,
        ],
        EventParticipantRegistered::class => [
            RegisterEventParticipant::class,
        ],
        StaffAssignedToEvent::class => [
            AssignStaffToEvent::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
