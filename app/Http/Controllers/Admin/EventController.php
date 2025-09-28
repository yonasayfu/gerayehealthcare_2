<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreateEventDTO;
use App\Enums\RoleEnum;
use App\Http\Controllers\Base\BaseController;
use App\Models\Event;
use App\Models\Staff;
use App\Services\Event\EventService;
use App\Services\Validation\Rules\EventRules;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EventController extends BaseController
{
    public function __construct(EventService $eventService)
    {
        parent::__construct(
            $eventService,
            EventRules::class,
            'Admin/Events',
            'events',
            Event::class,
            CreateEventDTO::class
        );
        $this->middleware('role:'.RoleEnum::SUPER_ADMIN->value.'|'.RoleEnum::ADMIN->value);
    }

    public function export(Request $request)
    {
        return app(EventService::class)->export($request);
    }

    public function printCurrent(Request $request)
    {
        return app(EventService::class)->printCurrent($request);
    }

    public function printSingle(Request $request, Event $event)
    {
        return app(EventService::class)->printSingle($request, $event);
    }

    public function create()
    {
        $staff = Staff::select('id', 'first_name', 'last_name')->orderBy('first_name')->get();

        return Inertia::render($this->viewName.'/Create', [
            'staff' => $staff,
        ]);
    }

    public function edit($id)
    {
        $event = $this->service->getById($id);
        $staff = Staff::select('id', 'first_name', 'last_name')->orderBy('first_name')->get();

        return Inertia::render($this->viewName.'/Edit', [
            lcfirst(class_basename($this->modelClass)) => $event,
            'staff' => $staff,
        ]);
    }
}
