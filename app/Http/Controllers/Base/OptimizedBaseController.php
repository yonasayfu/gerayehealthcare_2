<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

abstract class OptimizedBaseController extends Controller
{
    protected $service;

    protected $validationRules;

    protected $viewName;

    protected $routeName;

    protected $modelClass;

    protected $dtoClass;

    // Define eager loading relationships for each action
    protected $indexWith = [];

    protected $showWith = [];

    protected $createWith = [];

    protected $editWith = [];

    public function __construct($service, $validationRules, $viewName, $routeName, $modelClass, $dtoClass = null)
    {
        $this->service = $service;
        $this->validationRules = $validationRules;
        $this->viewName = $viewName;
        $this->routeName = $routeName;
        $this->modelClass = $modelClass;
        $this->dtoClass = $dtoClass;
    }

    public function index(Request $request)
    {
        // Use eager loading to prevent N+1 queries
        $data = $this->service->getAll($request, $this->indexWith);

        return Inertia::render($this->viewName.'/Index', [
            lcfirst(class_basename($this->modelClass)).'s' => $data,
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page']),
        ]);
    }

    public function show($id)
    {
        // Use eager loading for show action
        $data = $this->service->getById($id, $this->showWith);

        return Inertia::render($this->viewName.'/Show', [
            lcfirst(class_basename($this->modelClass)) => $data,
        ]);
    }

    public function create()
    {
        // Load related data only if needed
        $relatedData = $this->loadRelatedDataForCreate();

        return Inertia::render($this->viewName.'/Create', $relatedData);
    }

    public function edit($id)
    {
        // Use eager loading for edit action
        $data = $this->service->getById($id, $this->editWith);
        $relatedData = $this->loadRelatedDataForEdit();

        return Inertia::render($this->viewName.'/Edit', array_merge([
            lcfirst(class_basename($this->modelClass)) => $data,
        ], $relatedData));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate($this->validationRules::create());

        // Use DTO if defined, otherwise use array
        if ($this->dtoClass) {
            $dto = $this->dtoClass::from($validatedData);
            $data = $this->service->create($dto);
        } else {
            $data = $this->service->create($validatedData);
        }

        return redirect()->route('admin.'.$this->routeName.'.index')
            ->with('banner', class_basename($this->modelClass).' created successfully!')
            ->with('bannerStyle', 'success');
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate($this->validationRules::update());

        // Use DTO if defined, otherwise use array
        if ($this->dtoClass) {
            $dto = $this->dtoClass::from($validatedData);
            $data = $this->service->update($id, $dto);
        } else {
            $data = $this->service->update($id, $validatedData);
        }

        return redirect()->route('admin.'.$this->routeName.'.index')
            ->with('banner', class_basename($this->modelClass).' updated successfully!')
            ->with('bannerStyle', 'success');
    }

    public function destroy($id)
    {
        $this->service->delete($id);

        return redirect()->route('admin.'.$this->routeName.'.index')
            ->with('banner', class_basename($this->modelClass).' deleted successfully!')
            ->with('bannerStyle', 'success');
    }

    // Override these methods in child controllers to load related data efficiently
    protected function loadRelatedDataForCreate(): array
    {
        return [];
    }

    protected function loadRelatedDataForEdit(): array
    {
        return [];
    }
}
