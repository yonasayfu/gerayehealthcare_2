<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str; // Import Str facade
use Illuminate\Support\Facades\Log;

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
        return Inertia::render($this->viewName . '/Index', [
            $this->dataVariableName => $data,
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page', 'sort_by', 'sort_order'])
        ]);
    }

    public function create()
    {
        return Inertia::render($this->viewName . '/Create');
    }

    public function store(Request $request)
    {
        $validatedData = $this->validateRequest($request, 'store');

        $data = $this->dtoClass ? new ($this->dtoClass)(...$this->prepareDataForDTO($validatedData)) : $validatedData;


        $this->service->create($data);

        return redirect()->route('admin.' . $this->getRouteName() . '.index')->with('success', ucfirst($this->dataVariableName) . ' created successfully.');
    }

    public function show($id)
    {
        $data = $this->service->getById($id);
        Log::info('Data retrieved in BaseController@show:', ['data' => $data]);

        // Conditionally add ethiopian_date for EthiopianCalendarDay model
        if ($this->modelClass === \App\Models\EthiopianCalendarDay::class && $data->gregorian_date) {
            try {
                $gregorianDate = new \DateTime($data->gregorian_date);
                $ethiopicDate = \Andegna\DateTimeFactory::fromDateTime($gregorianDate);
                $data->ethiopian_date = $ethiopicDate->format('Y-m-d');
            } catch (\Exception $e) {
                Log::error("Error converting Gregorian date to Ethiopian in BaseController@show: " . $e->getMessage());
                $data->ethiopian_date = null; // Ensure it's null on error
            }
        }

        // Ensure the prop name matches the frontend's camelCase expectation
        $propName = lcfirst(class_basename($this->modelClass));
        return Inertia::render($this->viewName . '/Show', [$propName => $data]);
    }

    public function edit($id)
    {
        $data = $this->service->getById($id);
        // Ensure the prop name matches the frontend's camelCase expectation
        $propName = lcfirst(class_basename($this->modelClass));
        return Inertia::render($this->viewName . '/Edit', [$propName => $data]);
    }

    public function update(Request $request, $id)
    {
        $model = $this->service->getById($id);
        $validatedData = $this->validateRequest($request, 'update', $model);

        $data = $this->dtoClass ? new ($this->dtoClass)(...$this->prepareDataForDTO($validatedData)) : $validatedData;

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
        $routeName = Str::kebab(Str::plural(class_basename($this->modelClass)));
        Log::info('Generated route name:', ['name' => $routeName, 'model' => class_basename($this->modelClass)]);
        return $routeName;
    }

    private function validateRequest(Request $request, $method, $model = null)
    {
        $rules = $this->rulesClass::$method($model);
        return $request->validate($rules);
    }

    /**
     * Prepares data for DTO instantiation, ensuring all expected properties are present.
     *
     * @param array $validatedData
     * @return array
     */
    protected function prepareDataForDTO(array $validatedData): array
    {
        Log::info('Validated data before DTO preparation:', ['validatedData' => $validatedData]);
        if (!$this->dtoClass) {
            return $validatedData;
        }

        $reflectionClass = new \ReflectionClass($this->dtoClass);
        $constructor = $reflectionClass->getConstructor();

        if (!$constructor) {
            return $validatedData;
        }

        $parameters = $constructor->getParameters();
        $dtoData = [];

        foreach ($parameters as $parameter) {
            $name = $parameter->getName();
            if (array_key_exists($name, $validatedData)) {
                $dtoData[$name] = $validatedData[$name];
            } elseif ($parameter->isDefaultValueAvailable()) {
                $dtoData[$name] = $parameter->getDefaultValue();
            } elseif ($parameter->allowsNull()) {
                $dtoData[$name] = null;
            } else {
                // Log error or throw exception for missing required parameters
                throw new \InvalidArgumentException("Missing required parameter: {$name} for DTO {$this->dtoClass}");
            }
        }

        return $dtoData;
    }
}
