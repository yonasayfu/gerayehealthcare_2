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
            Event::class,
            CreateEventDTO::class
        );
        $this->middleware('role:' . RoleEnum::SUPER_ADMIN->value . '|' . RoleEnum::ADMIN->value);
    }

    

  
}