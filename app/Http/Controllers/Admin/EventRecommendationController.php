<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreateEventRecommendationDTO;
use App\DTOs\UpdateEventRecommendationDTO;
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
            EventRecommendation::class,
            CreateEventRecommendationDTO::class
        );
        $this->middleware('role:' . RoleEnum::SUPER_ADMIN->value . '|' . RoleEnum::ADMIN->value);
    }

    
}
