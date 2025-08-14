<?php

namespace App\Http\Controllers\Admin;

use App\Models\Staff;
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
            Event::class,
            CreateEventDTO::class
        );
        $this->middleware('role:' . RoleEnum::SUPER_ADMIN->value . '|' . RoleEnum::ADMIN->value);
    }

    public function create()
    {
        $staff = Staff::select('id', 'first_name', 'last_name')->orderBy('first_name')->get();

        return Inertia::render($this->viewName . '/Create', [
            'staff' => $staff,
        ]);
    }

    public function edit($id)
    {
        $event = $this->service->getById($id);
        $staff = Staff::select('id', 'first_name', 'last_name')->orderBy('first_name')->get();

        return Inertia::render($this->viewName . '/Edit', [
            lcfirst(class_basename($this->modelClass)) => $event,
            'staff' => $staff,
        ]);
    }
}