<?php

namespace App\Providers;

use App\Events\CaregiverAssigned;
use App\Events\EventParticipantRegistered;
use App\Events\InventoryRequestSaved;
use App\Events\MessageDeleted;
use App\Events\MessageReacted;
use App\Events\MessageUpdated;
use App\Events\NewMessage;
use App\Events\PatientCreatedFromRecommendation;
use App\Events\StaffAssignedToEvent;
use App\Listeners\AssignStaffToEvent;
use App\Listeners\CheckForLowStock;
use App\Listeners\CreatePatientFromRecommendation;
use App\Listeners\RegisterEventParticipant;
use App\Listeners\SendCaregiverAssignmentNotification;
use App\Listeners\SendPushNotificationListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Notifications\Events\NotificationSent;
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
        NewMessage::class => [
            // Add listener for new message notifications
        ],
        MessageReacted::class => [
            // Add listener for message reactions
        ],
        MessageDeleted::class => [
            // Add listener for message cleanup
        ],
        MessageUpdated::class => [
            // Add listener for message updates
        ],
        NotificationSent::class => [
            SendPushNotificationListener::class,
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
