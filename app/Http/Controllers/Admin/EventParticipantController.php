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
            EventParticipant::class
        );
        $this->middleware('role:' . RoleEnum::SUPER_ADMIN->value . '|' . RoleEnum::ADMIN->value);
    }

    public function show(EventParticipant $eventParticipant)
    {
        return parent::show($eventParticipant->id);
    }

    public function edit(EventParticipant $eventParticipant)
    {
        return parent::edit($eventParticipant->id);
    }

    public function update(Request $request, EventParticipant $eventParticipant)
    {
        return parent::update($request, $eventParticipant->id);
    }

    public function destroy(EventParticipant $eventParticipant)
    {
        return parent::destroy($eventParticipant->id);
    }

    public function printSingle(EventParticipant $eventParticipant)
    {
        return parent::printSingle($eventParticipant->id);
    }
}