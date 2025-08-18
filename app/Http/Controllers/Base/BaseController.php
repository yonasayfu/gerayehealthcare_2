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
    protected $routeName; // Optional override for route base name

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
            'filters' => $request->only([
                'search', 'sort', 'direction', 'per_page',
                'sort_by', 'sort_order', 'active_only',
                'campaign_id', 'platform_id', 'status', 'period_start', 'period_end',
                'is_active', 'language'
            ])
        ]);
    }

    public function create()
    {
        return Inertia::render($this->viewName . '/Create');
    }

    public function store(Request $request)
    {
        $validatedData = $this->validateRequest($request, 'store');

        if ($this->dtoClass) {
            // Prepare data for DTO instantiation
            $dtoData = $this->prepareDataForDTO($validatedData);
            // Instantiate DTO with constructor parameters, not as a single array
            $dto = new ($this->dtoClass)(...array_values($dtoData));
            $payload = method_exists($dto, 'toArray') ? $dto->toArray() : $dtoData;
        } else {
            $payload = $validatedData;
        }

        $this->service->create($payload);

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
        Log::info('Data retrieved in BaseController@edit:', ['data' => $data]);
        // Ensure the prop name matches the frontend's camelCase expectation
        $propName = lcfirst(class_basename($this->modelClass));
        return Inertia::render($this->viewName . '/Edit', [$propName => $data]);
    }

    public function update(Request $request, $id)
    {
        $model = $this->service->getById($id);
        $validatedData = $this->validateRequest($request, 'update', $model);

        if ($this->dtoClass) {
            // Prepare data for DTO instantiation
            $dtoData = $this->prepareDataForDTO($validatedData);
            // Instantiate DTO with constructor parameters, not as a single array
            $dto = new ($this->dtoClass)(...array_values($dtoData));
            $payload = method_exists($dto, 'toArray') ? $dto->toArray() : $dtoData;
        } else {
            $payload = $validatedData;
        }

        $this->service->update($id, $payload);

        return redirect()->route('admin.' . $this->getRouteName() . '.index')->with('success', ucfirst($this->dataVariableName) . ' updated successfully.');
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return back()->with('success', ucfirst($this->dataVariableName) . ' deleted successfully.');
    }

    

    private function getRouteName()
    {
        if (!empty($this->routeName)) {
            return $this->routeName;
        }
        $routeName = Str::kebab(Str::plural(class_basename($this->modelClass)));
        return $routeName;
    }

    protected function validateRequest(Request $request, $method, $model = null)
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

        if (!is_array($parameters) && !($parameters instanceof \Traversable)) {
            return $validatedData;
        }

        foreach ($parameters as $parameter) {
            $name = $parameter->getName();
            if (array_key_exists($name, $validatedData)) {
                $value = $validatedData[$name];
                // Ensure staff_id is always an int
                if ($name === 'staff_id') {
                    \Log::debug('prepareDataForDTO: staff_id value', ['value' => $value]);
                    if (is_array($value)) {
                        // Try to extract a numeric value from the array
                        if (isset($value['id']) && is_numeric($value['id'])) {
                            $value = $value['id'];
                        } else {
                            // Try to get the first numeric value in the array
                            $numeric = array_filter($value, 'is_numeric');
                            if (!empty($numeric)) {
                                $value = array_shift($numeric);
                            } else {
                                throw new \InvalidArgumentException('staff_id must be an integer or contain an id key. Got: ' . json_encode($value));
                            }
                        }
                    }
                    $value = (int) $value;
                }
                $dtoData[$name] = $value;
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
