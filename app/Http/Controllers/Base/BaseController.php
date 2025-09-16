<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str; // Import Str facade
use Inertia\Inertia;

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

        return Inertia::render($this->viewName.'/Index', [
            $this->dataVariableName => $data,
            'filters' => $request->only([
                'search', 'sort', 'direction', 'per_page',
                'sort_by', 'sort_order', 'active_only',
                'campaign_id', 'platform_id', 'status', 'period_start', 'period_end',
                'is_active', 'language',
            ]),
        ]);
    }

    public function create()
    {
        return Inertia::render($this->viewName.'/Create');
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $this->validateRequest($request, 'store');

            if ($this->dtoClass) {
                $dtoData = $this->prepareDataForDTO($validatedData);
                // For plain PHP DTOs (no static from() / toArray()), use the prepared array directly
                $payload = $dtoData;
                if ($this->dtoClass === \App\DTOs\CreateVisitServiceDTO::class && ! isset($payload['is_paid_to_staff'])) {
                    $payload['is_paid_to_staff'] = false;
                }
            } else {
                $payload = $validatedData;
            }

            $this->service->create($payload);

            // Explicitly flash data to session
            $request->session()->flash('banner', ucfirst($this->dataVariableName).' created successfully.');
            $request->session()->flash('bannerStyle', 'success');

            return redirect()->route('admin.'.$this->getRouteName().'.index');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput()->with('banner', 'Validation failed. Please check your input.')->with('bannerStyle', 'danger');
        } catch (\Exception $e) {
            Log::error('Error in BaseController store method:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return back()->withInput()->with('banner', 'An unexpected error occurred: '.$e->getMessage())->with('bannerStyle', 'danger');
        }
    }

    public function show($id)
    {
        $data = $this->service->getById($id);

        // Conditionally add ethiopian_date for EthiopianCalendarDay model
        if ($this->modelClass === \App\Models\EthiopianCalendarDay::class && $data->gregorian_date) {
            try {
                $gregorianDate = new \DateTime($data->gregorian_date);
                $ethiopicDate = \Andegna\DateTimeFactory::fromDateTime($gregorianDate);
                $data->ethiopian_date = $ethiopicDate->format('Y-m-d');
            } catch (\Exception $e) {
                Log::error('Error converting Gregorian date to Ethiopian in BaseController@show: '.$e->getMessage());
                $data->ethiopian_date = null; // Ensure it's null on error
            }
        }

        // Ensure the prop name matches the frontend's camelCase expectation
        $propName = lcfirst(class_basename($this->modelClass));

        return Inertia::render($this->viewName.'/Show', [$propName => $data]);
    }

    public function edit($id)
    {
        $data = $this->service->getById($id);
        // Ensure the prop name matches the frontend's camelCase expectation
        $propName = lcfirst(class_basename($this->modelClass));

        return Inertia::render($this->viewName.'/Edit', [$propName => $data]);
    }

    public function update(Request $request, $id)
    {
        try {
            $model = $this->service->getById($id);
            $validatedData = $this->validateRequest($request, 'update', $model);

            if ($this->dtoClass) {
                $dtoData = $this->prepareDataForDTO($validatedData);
                // For plain PHP DTOs (no static from() / toArray()), use the prepared array directly
                $payload = $dtoData;
            } else {
                $payload = $validatedData;
            }

            // Important: avoid overwriting existing DB values with nulls for fields not present
            $payload = array_filter($payload, function ($value) {
                return ! is_null($value);
            });

            $this->service->update($id, $payload);

            // Explicitly flash data to session
            $request->session()->flash('banner', ucfirst($this->dataVariableName).' updated successfully.');
            $request->session()->flash('bannerStyle', 'success');

            return redirect()->route('admin.'.$this->getRouteName().'.index');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput()->with('banner', 'Validation failed. Please check your input.')->with('bannerStyle', 'danger');
        } catch (\Exception $e) {
            Log::error('Error in BaseController update method:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return back()->withInput()->with('banner', 'An unexpected error occurred: '.$e->getMessage())->with('bannerStyle', 'danger');
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $this->service->delete($id);

            // Explicitly flash data to session
            $request->session()->flash('banner', ucfirst($this->dataVariableName).' deleted successfully.');
            $request->session()->flash('bannerStyle', 'danger');

            return redirect()->route('admin.'.$this->getRouteName().'.index');
        } catch (\Exception $e) {
            Log::error('Error in BaseController destroy method:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return back()->with('banner', 'An unexpected error occurred during deletion: '.$e->getMessage())->with('bannerStyle', 'danger');
        }
    }

    protected function getRouteName()
    {
        if (! empty($this->routeName)) {
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
     */
    protected function prepareDataForDTO(array $validatedData): array
    {
        if (! $this->dtoClass) {
            return $validatedData;
        }

        $reflectionClass = new \ReflectionClass($this->dtoClass);
        $constructor = $reflectionClass->getConstructor();

        if (! $constructor) {
            return $validatedData;
        }

        $parameters = $constructor->getParameters();
        $dtoData = [];

        if (! is_array($parameters) && ! ($parameters instanceof \Traversable)) {
            return $validatedData;
        }

        foreach ($parameters as $parameter) {
            $name = $parameter->getName();
            if (array_key_exists($name, $validatedData)) {
                $value = $validatedData[$name];
                // Ensure common FK ids are always integers
                if (in_array($name, ['event_id', 'staff_id', 'partner_id', 'signed_by_staff_id', 'sent_by_staff_id', 'agreement_id', 'referred_patient_id'], true)) {
                    if (is_array($value)) {
                        // Try to extract a numeric value from the array
                        if (isset($value['id']) && is_numeric($value['id'])) {
                            $value = $value['id'];
                        } else {
                            // Try to get the first numeric value in the array
                            $numeric = array_filter($value, 'is_numeric');
                            if (! empty($numeric)) {
                                $value = array_shift($numeric);
                            } else {
                                throw new \InvalidArgumentException($name.' must be an integer or contain an id key. Got: '.json_encode($value));
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
