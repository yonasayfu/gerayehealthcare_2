<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Models\Event;
use App\Models\EligibilityCriteria;
use App\Services\Validation\Rules\EligibilityCriteriaRules;
use App\DTOs\CreateEligibilityCriteriaDTO;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use App\Http\Requests\StoreEligibilityCriteriaRequest;
use App\Http\Requests\UpdateEligibilityCriteriaRequest;
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
        $this->middleware('role:' . \App\Enums\RoleEnum::SUPER_ADMIN->value . '|' . \App\Enums\RoleEnum::ADMIN->value);
    }

    public function create()
    {
        $events = Event::select('id', 'title')->orderBy('title')->get();

        return Inertia::render($this->viewName . '/Create', [
            'events' => $events,
        ]);
    }

    public function edit($id)
    {
        $eligibilityCriteria = $this->service->getById($id);
        $events = Event::select('id', 'title')->orderBy('title')->get();

        return Inertia::render($this->viewName . '/Edit', [
            lcfirst(class_basename($this->modelClass)) => $eligibilityCriteria,
            'events' => $events,
        ]);
    }
}
