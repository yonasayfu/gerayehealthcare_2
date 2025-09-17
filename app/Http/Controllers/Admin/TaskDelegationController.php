<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Models\Staff;
use App\Models\TaskDelegation;
use App\Services\TaskDelegation\TaskDelegationService;
use App\Services\Validation\Rules\TaskDelegationRules;
use Inertia\Inertia;

class TaskDelegationController extends BaseController
{
    public function __construct(TaskDelegationService $taskDelegationService)
    {
        parent::__construct(
            $taskDelegationService,
            TaskDelegationRules::class,
            'Admin/TaskDelegations',
            'taskDelegations',
            TaskDelegation::class,
            null
        );
    }

    public function create()
    {
        $prefill = [
            'title' => request()->query('title'),
            'assigned_to' => request()->query('assigned_to'),
            'due_date' => request()->query('due_date'),
            'status' => request()->query('status'),
            'notes' => request()->query('notes'),
            'task_category' => request()->query('task_category'),
            'partner_id' => request()->query('partner_id'),
        ];

        return Inertia::render('Admin/TaskDelegations/Create', [
            'staff' => Staff::all(['id', 'first_name', 'last_name']),
            'prefill' => $prefill,
            'returnTo' => request()->query('return_to'),
            'inventoryAlertId' => request()->query('inventory_alert_id'),
            'partners' => \App\Models\Partner::select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    public function edit($id)
    {
        $taskDelegation = $this->service->getById($id);

        return Inertia::render('Admin/TaskDelegations/Edit', [
            'taskDelegation' => $taskDelegation,
            'staff' => Staff::all(['id', 'first_name', 'last_name']),
            'partners' => \App\Models\Partner::select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    public function store(\Illuminate\Http\Request $request)
    {
        // Validate using base rules
        $validatedData = $this->validateRequest($request, 'store');

        if (empty($validatedData['assigned_to'])) {
            return back()->withErrors(['assigned_to' => 'The assigned to field is required.'])->withInput();
        }

        // Create the task via service
        $created = $this->service->create($validatedData);

        // If this came from an Inventory Alert, link it
        $inventoryAlertId = $request->input('inventory_alert_id');
        if ($inventoryAlertId) {
            try {
                \App\Models\InventoryAlert::where('id', $inventoryAlertId)
                    ->update(['delegated_task_id' => $created->id]);
            } catch (\Throwable $e) {
                \Log::error('Failed to link InventoryAlert to TaskDelegation', [
                    'inventory_alert_id' => $inventoryAlertId,
                    'task_id' => $created->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Redirect back to return_to if provided, else index
        $returnTo = $request->input('return_to');
        if ($returnTo) {
            return redirect()->to($returnTo)->with('banner', 'Task delegation created and linked successfully.')->with('bannerStyle', 'success');
        }

        return redirect()->route('admin.' . $this->getRouteName() . '.index')
            ->with('banner', ucfirst($this->dataVariableName) . ' created successfully.')->with('bannerStyle', 'success');
    }

    public function update(\Illuminate\Http\Request $request, $id)
    {
        $taskDelegation = $this->service->getById($id);
        $this->authorize('update', $taskDelegation);

        $validatedData = $this->validateRequest($request, 'update', $taskDelegation);

        $this->service->update($id, $validatedData);

        return redirect()->route('admin.' . $this->getRouteName() . '.index')
            ->with('banner', ucfirst($this->dataVariableName) . ' updated successfully.')->with('bannerStyle', 'success');
    }
}
