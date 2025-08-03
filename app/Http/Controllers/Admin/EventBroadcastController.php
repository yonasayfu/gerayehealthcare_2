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
            EventBroadcast::class
        );
        $this->middleware('role:' . RoleEnum::SUPER_ADMIN->value . '|' . RoleEnum::ADMIN->value);
    }

    public function show(EventBroadcast $eventBroadcast)
    {
        return parent::show($eventBroadcast->id);
    }

    public function edit(EventBroadcast $eventBroadcast)
    {
        return parent::edit($eventBroadcast->id);
    }

    public function update(Request $request, EventBroadcast $eventBroadcast)
    {
        return parent::update($request, $eventBroadcast->id);
    }

    public function destroy(EventBroadcast $eventBroadcast)
    {
        return parent::destroy($eventBroadcast->id);
    }

    public function printSingle(EventBroadcast $eventBroadcast)
    {
        return parent::printSingle($eventBroadcast->id);
    }
}