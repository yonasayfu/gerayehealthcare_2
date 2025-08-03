<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\EventRecommendationService;
use App\Models\EventRecommendation;
use App\Services\Validation\Rules\EventRecommendationRules;
use App\Enums\RoleEnum;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EventRecommendationController extends BaseController
{
    public function __construct(EventRecommendationService $eventRecommendationService)
    {
        parent::__construct(
            $eventRecommendationService,
            EventRecommendationRules::class,
            'Admin/EventRecommendations',
            'recommendations',
            EventRecommendation::class
        );
        $this->middleware('role:' . RoleEnum::SUPER_ADMIN->value . '|' . RoleEnum::ADMIN->value);
    }

    public function show(EventRecommendation $eventRecommendation)
    {
        return parent::show($eventRecommendation->id);
    }

    public function edit(EventRecommendation $eventRecommendation)
    {
        return parent::edit($eventRecommendation->id);
    }

    public function update(Request $request, EventRecommendation $eventRecommendation)
    {
        return parent::update($request, $eventRecommendation->id);
    }

    public function destroy(EventRecommendation $eventRecommendation)
    {
        return parent::destroy($eventRecommendation->id);
    }

    public function printSingle(EventRecommendation $eventRecommendation)
    {
        return parent::printSingle($eventRecommendation->id);
    }
}
