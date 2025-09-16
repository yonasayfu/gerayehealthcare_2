<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RoleEnum;
use App\Http\Controllers\Base\BaseController;
use App\DTOs\CreateEventParticipantDTO;
use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\Patient;
use App\Services\EventParticipant\EventParticipantService;
use App\Services\Validation\Rules\EventParticipantRules;
use Inertia\Inertia;

class EventParticipantController extends BaseController
{
    public function __construct(EventParticipantService $eventParticipantService)
    {
        parent::__construct(
            $eventParticipantService,
            EventParticipantRules::class,
            'Admin/EventParticipants',
            'participants',
            EventParticipant::class,
            CreateEventParticipantDTO::class
        );
        $this->middleware('role:'.RoleEnum::SUPER_ADMIN->value.'|'.RoleEnum::ADMIN->value);
    }

    public function create()
    {
        $events = Event::select('id', 'title')->orderBy('title')->get();
        $patients = Patient::select('id', 'full_name')->orderBy('full_name')->get();

        return Inertia::render($this->viewName.'/Create', [
            'events' => $events,
            'patients' => $patients,
        ]);
    }

    public function edit($id)
    {
        $eventParticipant = $this->service->getById($id);
        $events = Event::select('id', 'title')->orderBy('title')->get();
        $patients = Patient::select('id', 'full_name')->orderBy('full_name')->get();

        return Inertia::render($this->viewName.'/Edit', [
            lcfirst(class_basename($this->modelClass)) => $eventParticipant,
            'events' => $events,
            'patients' => $patients,
        ]);
    }
}
