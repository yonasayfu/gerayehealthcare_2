<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\AdditionalExportConfigs;
use App\Models\EventBroadcast;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class EventBroadcastController extends Controller
{
    use ExportableTrait;
    public function __construct()
    {
        $this->middleware('role:' . \App\Enums\RoleEnum::SUPER_ADMIN->value . '|' . \App\Enums\RoleEnum::ADMIN->value);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = EventBroadcast::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('message', 'ilike', "%{$search}%")
                  ->orWhere('channel', 'ilike', "%{$search}%");
        }

        if ($request->filled('sort') && !empty($request->input('sort'))) {
            $sortField = $request->input('sort');
            $sortDirection = $request->input('direction', 'asc');

            $sortableFields = ['event_id', 'channel', 'sent_by_staff_id', 'created_at'];
            if (in_array($sortField, $sortableFields)) {
                $query->orderBy($sortField, $sortDirection);
            } else {
                $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $broadcasts = $query->paginate($request->input('per_page', 5))->withQueryString();

        return Inertia::render('Admin/EventBroadcasts/Index', [
            'broadcasts' => $broadcasts,
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/EventBroadcasts/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'channel' => 'required|string|max:255',
            'message' => 'required|string',
            'sent_by_staff_id' => 'required|exists:staff,id',
        ]);

        EventBroadcast::create($validated);

        return redirect()->route('admin.event-broadcasts.index')
            ->with('success', 'Event broadcast created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(EventBroadcast $eventBroadcast)
    {
        return Inertia::render('Admin/EventBroadcasts/Show', [
            'broadcast' => $eventBroadcast,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EventBroadcast $eventBroadcast)
    {
        return Inertia::render('Admin/EventBroadcasts/Edit', [
            'broadcast' => $eventBroadcast,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EventBroadcast $eventBroadcast)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'channel' => 'required|string|max:255',
            'message' => 'required|string',
            'sent_by_staff_id' => 'required|exists:staff,id',
        ]);

        $eventBroadcast->update($validated);

        return redirect()->route('admin.event-broadcasts.index')
            ->with('success', 'Event broadcast updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EventBroadcast $eventBroadcast)
    {
        $eventBroadcast->delete();

        return redirect()->route('admin.event-broadcasts.index')
            ->with('success', 'Event broadcast deleted successfully.');
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, EventBroadcast::class, AdditionalExportConfigs::getEventBroadcastConfig());
    }

    public function printSingle(EventBroadcast $eventBroadcast)
    {
        return $this->handlePrintSingle($eventBroadcast, AdditionalExportConfigs::getEventBroadcastConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, EventBroadcast::class, AdditionalExportConfigs::getEventBroadcastConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, EventBroadcast::class, AdditionalExportConfigs::getEventBroadcastConfig());
    }
}
