<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\RoleService;
use App\Services\Validation\Rules\RoleRules;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends BaseController
{
    public function __construct(RoleService $roleService)
    {
        parent::__construct(
            $roleService,
            RoleRules::class,
            'Admin/Roles',
            'roles',
            Role::class
        );
    }

    /**
     * Display a listing of the resource.
     */
    public function index(\Illuminate\Http\Request $request)
    {
        $data = $this->service->getAll($request, ['permissions']); // Eager load permissions

        // Transform the data to ensure permissions are properly formatted
        if (isset($data['data'])) {
            $data['data'] = collect($data['data'])->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'permissions' => $role->permissions->map(function ($permission) {
                        return [
                            'id' => $permission->id,
                            'name' => $permission->name,
                        ];
                    }),
                ];
            });
        }

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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $allPermissions = Permission::pluck('name')->toArray();

        return Inertia::render($this->viewName.'/Create', [
            'permissions' => $allPermissions,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $role = $this->service->getById($id);
        $role->load('permissions'); // Ensure permissions are loaded
        $allPermissions = Permission::pluck('name')->toArray();

        return Inertia::render($this->viewName.'/Edit', [
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role->permissions->map(function ($permission) {
                    return ['name' => $permission->name];
                }),
            ],
            'allPermissions' => $allPermissions,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(\Illuminate\Http\Request $request)
    {
        try {
            $validatedData = $this->validateRequest($request, 'store');

            $role = $this->service->create(['name' => $validatedData['name']]);
            $role->syncPermissions($validatedData['permissions'] ?? []);

            $request->session()->flash('banner', ucfirst($this->dataVariableName).' created successfully.');
            $request->session()->flash('bannerStyle', 'success');

            return redirect()->route('admin.'.$this->getRouteName().'.index');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput()->with('banner', 'Validation failed. Please check your input.')->with('bannerStyle', 'danger');
        } catch (\Exception $e) {
            Log::error('Error in RoleController store method:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return back()->withInput()->with('banner', 'An unexpected error occurred: '.$e->getMessage())->with('bannerStyle', 'danger');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(\Illuminate\Http\Request $request, $id)
    {
        try {
            $role = $this->service->getById($id);
            $validatedData = $this->validateRequest($request, 'update', $role);

            $this->service->update($id, ['name' => $validatedData['name']]);
            $role->syncPermissions($validatedData['permissions'] ?? []);

            $request->session()->flash('banner', ucfirst($this->dataVariableName).' updated successfully.');
            $request->session()->flash('bannerStyle', 'success');

            return redirect()->route('admin.'.$this->getRouteName().'.index');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput()->with('banner', 'Validation failed. Please check your input.')->with('bannerStyle', 'danger');
        } catch (\Exception $e) {
            Log::error('Error in RoleController update method:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return back()->withInput()->with('banner', 'An unexpected error occurred: '.$e->getMessage())->with('bannerStyle', 'danger');
        }
    }
}
