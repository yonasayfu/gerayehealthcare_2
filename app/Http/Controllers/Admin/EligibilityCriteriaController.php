<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreateEligibilityCriteriaDTO;
use App\Http\Controllers\Base\BaseController;
use App\Models\EligibilityCriteria;
use App\Models\Event;
use App\Services\EligibilityCriteriaService;
use App\Services\Validation\Rules\EligibilityCriteriaRules;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EligibilityCriteriaController extends BaseController
{
    public function __construct(EligibilityCriteriaService $eligibilityCriteriaService)
    {
        parent::__construct(
            $eligibilityCriteriaService,
            EligibilityCriteriaRules::class,
            'Admin/EligibilityCriteria',
            'eligibilityCriteria',
            EligibilityCriteria::class,
            CreateEligibilityCriteriaDTO::class
        );
        // Ensure route names use 'eligibility-criteria' (resource name in routes/web.php)
        $this->routeName = 'eligibility-criteria';
        $this->middleware('role:'.\App\Enums\RoleEnum::SUPER_ADMIN->value.'|'.\App\Enums\RoleEnum::ADMIN->value);
    }

    public function create()
    {
        $events = Event::select('id', 'title')->orderBy('title')->get();

        return Inertia::render($this->viewName.'/Create', [
            'events' => $events,
        ]);
    }

    public function edit($id)
    {
        $eligibilityCriteria = $this->service->getById($id);
        $events = Event::select('id', 'title')->orderBy('title')->get();

        return Inertia::render($this->viewName.'/Edit', [
            lcfirst(class_basename($this->modelClass)) => $eligibilityCriteria,
            'events' => $events,
        ]);
    }

    public function export(Request $request)
    {
        return $this->service->export($request);
    }

    public function printCurrent(Request $request)
    {
        return $this->service->printCurrent($request);
    }

    public function printSingle(Request $request, EligibilityCriteria $eligibility_criterion)
    {
        return $this->service->printSingle($request, $eligibility_criterion);
    }
}
