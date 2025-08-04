<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class BaseController extends Controller
{
    protected $service;
    protected $rulesClass;
    protected $viewName;
    protected $dataVariableName;
    protected $modelClass;
    protected $dtoClass; // New property for DTO class

    public function __construct($service, $rulesClass, $viewName, $dataVariableName, $modelClass, $dtoClass = null)
    {
        $this->service = $service;
        $this->rulesClass = $rulesClass;
        $this->viewName = $viewName;
        $this->dataVariableName = $dataVariableName;
        $this->modelClass = $modelClass;
        $this->dtoClass = $dtoClass; // Assign the DTO class
    }

    public function index(Request $request)
    {
        $data = $this->service->getAll($request);
        return Inertia::render($this->viewName . '/Index', [$this->dataVariableName => $data]);
    }

    public function create()
    {
        return Inertia::render($this->viewName . '/Create');
    }

    public function store(Request $request)
    {
        $validatedData = $this->validateRequest($request, 'store');

        $data = $this->dtoClass ? new ($this->dtoClass)(...$validatedData) : $validatedData;

        $this->service->create($data);

        return redirect()->route('admin.' . $this->getRouteName() . '.index')->with('success', ucfirst($this->dataVariableName) . ' created successfully.');
    }

    public function show($id)
    {
        $data = $this->service->getById($id);
        return Inertia::render($this->viewName . '/Show', [strtolower(class_basename($this->modelClass)) => $data]);
    }

    public function edit($id)
    {
        $data = $this->service->getById($id);
        return Inertia::render($this->viewName . '/Edit', [strtolower(class_basename($this->modelClass)) => $data]);
    }

    public function update(Request $request, $id)
    {
        $model = $this->service->getById($id);
        $validatedData = $this->validateRequest($request, 'update', $model);

        $data = $this->dtoClass ? new ($this->dtoClass)(...$validatedData) : $validatedData;

        $this->service->update($id, $data);

        return redirect()->route('admin.' . $this->getRouteName() . '.index')->with('success', ucfirst($this->dataVariableName) . ' updated successfully.');
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return back()->with('success', ucfirst($this->dataVariableName) . ' deleted successfully.');
    }

    public function export(Request $request)
    {
        return $this->service->export($request);
    }

    public function printSingle($id)
    {
        return $this->service->printSingle($id);
    }

    public function printCurrent(Request $request)
    {
        return $this->service->printCurrent($request);
    }

    public function printAll(Request $request)
    {
        return $this->service->printAll($request);
    }

    private function getRouteName()
    {
        return strtolower(str_plural(class_basename($this->modelClass)));
    }

    private function validateRequest(Request $request, $method, $model = null)
    {
        $rules = $this->rulesClass::$method($model);
        return $request->validate($rules);
    }
}
