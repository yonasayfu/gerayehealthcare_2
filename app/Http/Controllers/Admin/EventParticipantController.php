<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\EventParticipantService;
use App\Models\EventParticipant;
use App\Services\Validation\Rules\EventParticipantRules;
use App\Enums\RoleEnum;
use Illuminate\Http\Request;

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
        $this->middleware('role:' . RoleEnum::SUPER_ADMIN->value . '|' . RoleEnum::ADMIN->value);
    }

   
}