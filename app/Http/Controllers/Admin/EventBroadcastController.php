<?php

namespace App\Http\Controllers\Admin;

use App\Models\Event;
use App\Models\Staff;
use App\Services\Validation\Rules\EventBroadcastRules;
use App\Enums\RoleEnum;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Base\BaseController;

class EventBroadcastController extends BaseController
{
    public function __construct(EventBroadcastService $eventBroadcastService)
    {
        parent::__construct(
            $eventBroadcastService,
            EventBroadcastRules::class,
            'Admin/EventBroadcasts',
            'broadcasts',
            EventBroadcast::class,
            CreateEventBroadcastDTO::class
        );
        $this->middleware('role:' . RoleEnum::SUPER_ADMIN->value . '|' . RoleEnum::ADMIN->value);
    }

    public function create()
    {
        $events = Event::select('id', 'title')->orderBy('title')->get();
        $staff = Staff::select('id', 'first_name', 'last_name')->orderBy('first_name')->get();

        return Inertia::render($this->viewName . '/Create', [
            'events' => $events,
            'staff' => $staff,
        ]);
    }

    public function edit($id)
    {
        $eventBroadcast = $this->service->getById($id);
        $events = Event::select('id', 'title')->orderBy('title')->get();
        $staff = Staff::select('id', 'first_name', 'last_name')->orderBy('first_name')->get();

        return Inertia::render($this->viewName . '/Edit', [
            lcfirst(class_basename($this->modelClass)) => $eventBroadcast,
            'events' => $events,
            'staff' => $staff,
        ]);
    }
}