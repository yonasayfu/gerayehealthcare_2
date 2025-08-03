<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\EventService;
use App\Models\Event;
use App\Services\Validation\Rules\EventRules;
use App\Enums\RoleEnum;
use Inertia\Inertia;
use Illuminate\Http\Request;

class EventController extends BaseController
{
    public function __construct(EventService $eventService)
    {
        parent::__construct(
            $eventService,
            EventRules::class,
            'Admin/Events',
            'events',
            Event::class
        );
        $this->middleware('role:' . RoleEnum::SUPER_ADMIN->value . '|' . RoleEnum::ADMIN->value);
    }

    public function create()
    {
        // Not implemented for now
        return Inertia::render('Admin/Events/Create');
    }

    public function show(Event $event)
    {
        return parent::show($event->id);
    }

    public function edit(Event $event)
    {
        // Not implemented for now
        return Inertia::render('Admin/Events/Edit', [
            'event' => $event,
        ]);
    }

    public function update(Request $request, Event $event)
    {
        return parent::update($request, $event->id);
    }

    public function destroy(Event $event)
    {
        return parent::destroy($event->id);
    }

    public function printSingle(Event $event)
    {
        return parent::printSingle($event->id);
    }
}