<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\EventBroadcastService;
use App\Models\EventBroadcast;
use App\Services\Validation\Rules\EventBroadcastRules;
use App\Enums\RoleEnum;
use Illuminate\Http\Request;

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

   
}