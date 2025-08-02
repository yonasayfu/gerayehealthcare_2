<?php

namespace App\Http\Controllers\Admin;

use App\Models\EventBroadcast;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;

class EventBroadcastController extends Controller
{
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
        $type = $request->get('type');
        $broadcasts = EventBroadcast::select('event_id', 'channel', 'message', 'sent_by_staff_id')->get();

        if ($type === 'csv') {
            $csvData = "Event ID,Channel,Message,Sent By Staff ID\n";
            foreach ($broadcasts as $broadcast) {
                $csvData .= "\"{$broadcast->event_id}\",\"{$broadcast->channel}\",\"{$broadcast->message}\",\"{$broadcast->sent_by_staff_id}\"\n";
            }

            return Response::make($csvData, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="event-broadcasts.csv"',
            ]);
        }

        if ($type === 'pdf') {
            $data = $broadcasts->map(function($broadcast) {
                return [
                    'event_id' => $broadcast->event_id,
                    'channel' => $broadcast->channel,
                    'message' => $broadcast->message,
                    'sent_by_staff_id' => $broadcast->sent_by_staff_id,
                ];
            })->toArray();

            $columns = [
                ['key' => 'event_id', 'label' => 'Event ID'],
                ['key' => 'channel', 'label' => 'Channel'],
                ['key' => 'message', 'label' => 'Message'],
                ['key' => 'sent_by_staff_id', 'label' => 'Sent By Staff ID'],
            ];

            $title = 'Event Broadcasts List';
            $documentTitle = 'Event Broadcasts List';

            $pdf = Pdf::loadView('print-layout', compact('title', 'data', 'columns', 'documentTitle'))
                        ->setPaper('a4', 'landscape');
            return $pdf->stream('event-broadcasts.pdf');
        }

        return abort(400, 'Invalid export type');
    }

    public function printSingle(EventBroadcast $eventBroadcast)
    {
        $data = [
            ['label' => 'Event ID', 'value' => $eventBroadcast->event_id],
            ['label' => 'Channel', 'value' => $eventBroadcast->channel],
            ['label' => 'Message', 'value' => $eventBroadcast->message],
            ['label' => 'Sent By Staff ID', 'value' => $eventBroadcast->sent_by_staff_id],
        ];

        $columns = [
            ['key' => 'label', 'label' => 'Field', 'printWidth' => '30%'],
            ['key' => 'value', 'label' => 'Value', 'printWidth' => '70%'],
        ];

        $title = 'Event Broadcast Details';
        $documentTitle = 'Event Broadcast Details';

        $pdf = Pdf::loadView('print-layout', compact('title', 'data', 'columns', 'documentTitle'))
                    ->setPaper('a4', 'portrait');
        return $pdf->stream("event-broadcast-{$eventBroadcast->id}.pdf");
    }

    public function printCurrent(Request $request)
    {
        $query = EventBroadcast::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('message', 'ilike', "%{$search}%")
                  ->orWhere('channel', 'ilike', "%{$search}%");
        }

        if ($request->filled('sort') && !empty($request->input('sort'))) {
            $query->orderBy($request->input('sort'), $request->input('direction', 'asc'));
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $broadcasts = $query->get();

        $data = $broadcasts->map(function($broadcast) {
            return [
                'event_id' => $broadcast->event_id,
                'channel' => $broadcast->channel,
                'message' => $broadcast->message,
                'sent_by_staff_id' => $broadcast->sent_by_staff_id,
            ];
        })->toArray();

        $columns = [
            ['key' => 'event_id', 'label' => 'Event ID'],
            ['key' => 'channel', 'label' => 'Channel'],
            ['key' => 'message', 'label' => 'Message'],
            ['key' => 'sent_by_staff_id', 'label' => 'Sent By Staff ID'],
        ];

        $title = 'Event Broadcasts List (Current View)';
        $documentTitle = 'Event Broadcasts List (Current View)';

        $pdf = Pdf::loadView('print-layout', compact('title', 'data', 'columns', 'documentTitle'))
                    ->setPaper('a4', 'landscape');
        return $pdf->stream('event-broadcasts-current.pdf');
    }

    public function printAll(Request $request)
    {
        $broadcasts = EventBroadcast::orderBy('channel')->get();

        $data = $broadcasts->map(function($broadcast) {
            return [
                'event_id' => $broadcast->event_id,
                'channel' => $broadcast->channel,
                'message' => $broadcast->message,
                'sent_by_staff_id' => $broadcast->sent_by_staff_id,
            ];
        })->toArray();

        $columns = [
            ['key' => 'event_id', 'label' => 'Event ID'],
            ['key' => 'channel', 'label' => 'Channel'],
            ['key' => 'message', 'label' => 'Message'],
            ['key' => 'sent_by_staff_id', 'label' => 'Sent By Staff ID'],
        ];

        $title = 'Event Broadcasts List';
        $documentTitle = 'Event Broadcasts List';

        $pdf = Pdf::loadView('print-layout', compact('title', 'data', 'columns', 'documentTitle'))
                    ->setPaper('a4', 'landscape');
        return $pdf->stream('event-broadcasts.pdf');
    }
}
