<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreateEventRecommendationDTO;
use App\Enums\RoleEnum;
use App\Http\Controllers\Base\BaseController;
use App\Models\Event;
use App\Models\EventRecommendation;
use App\Models\Patient;
use App\Services\EventRecommendationService;
use App\Services\Validation\Rules\EventRecommendationRules;
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
            EventRecommendation::class,
            CreateEventRecommendationDTO::class
        );
        $this->middleware('role:'.RoleEnum::SUPER_ADMIN->value.'|'.RoleEnum::ADMIN->value);
    }

    public function export(Request $request)
    {
        return app(EventRecommendationService::class)->export($request);
    }

    public function printCurrent(Request $request)
    {
        return app(EventRecommendationService::class)->printCurrent($request);
    }

    public function printSingle(Request $request, EventRecommendation $event_recommendation)
    {
        return app(EventRecommendationService::class)->printSingle($request, $event_recommendation);
    }

    public function create()
    {
        $events = Event::select('id', 'title')->orderBy('title')->get();
        $patients = Patient::select('id', 'full_name')->orderBy('full_name')->get();

        return Inertia::render($this->viewName.'/Create', [
            'events' => $events,
            'patients' => $patients,
        ]);
    }

    public function edit($id)
    {
        $eventRecommendation = $this->service->getById($id);
        $events = Event::select('id', 'title')->orderBy('title')->get();
        $patients = Patient::select('id', 'full_name')->orderBy('full_name')->get();

        return Inertia::render($this->viewName.'/Edit', [
            lcfirst(class_basename($this->modelClass)) => $eventRecommendation,
            'events' => $events,
            'patients' => $patients,
        ]);
    }
}
