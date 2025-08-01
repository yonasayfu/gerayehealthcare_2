<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super_admin|admin');
    }

    public function index(Request $request)
    {
        $query = Event::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('title', 'ilike', "%{$search}%")
                  ->orWhere('description', 'ilike', "%{$search}%");
        }

        if ($request->filled('sort') && !empty($request->input('sort'))) {
            $sortField = $request->input('sort');
            $sortDirection = $request->input('direction', 'asc');

            $sortableFields = ['title', 'event_date', 'broadcast_status', 'created_at'];
            if (in_array($sortField, $sortableFields)) {
                $query->orderBy($sortField, $sortDirection);
            } else {
                $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $events = $query->paginate($request->input('per_page', 10))->withQueryString();

        return Inertia::render('Admin/Events/Index', [
            'events' => $events,
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page']),
        ]);
    }

    public function create()
    {
        // Not implemented for now
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'is_free_service' => 'boolean',
            'broadcast_status' => 'required|string|in:Draft,Published,Archived',
        ]);

        Event::create($validated);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event created successfully.');
    }

    public function show(Event $event)
    {
        return Inertia::render('Admin/Events/Show', [
            'event' => $event,
        ]);
    }

    public function edit(Event $event)
    {
        // Not implemented for now
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'is_free_service' => 'boolean',
            'broadcast_status' => 'required|string|in:Draft,Published,Archived',
        ]);

        $event->update($validated);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', 'Event deleted successfully.');
    }

    public function export(Request $request)
    {
        $type = $request->get('type');
        $events = Event::select('title', 'description', 'event_date', 'is_free_service', 'broadcast_status')->get();

        if ($type === 'csv') {
            $csvData = "Title,Description,Event Date,Is Free Service,Broadcast Status\n";
            foreach ($events as $event) {
                $csvData .= "\"{$event->title}\",\"{$event->description}\",\"{$event->event_date}\",\"{$event->is_free_service}\",\"{$event->broadcast_status}\"\n";
            }

            return Response::make($csvData, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="events.csv"',
            ]);
        }

        if ($type === 'pdf') {
            $pdf = Pdf::loadView('pdf.events', ['events' => $events])->setPaper('a4', 'landscape');
            return $pdf->stream('events.pdf');
        }

        return abort(400, 'Invalid export type');
    }

    public function printSingle(Event $event)
    {
        $pdf = Pdf::loadView('pdf.event-single', ['event' => $event])->setPaper('a4', 'portrait');
        return $pdf->stream("event-{$event->id}.pdf");
    }

    public function printCurrent(Request $request)
    {
        $query = Event::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('title', 'ilike', "%{$search}%")
                  ->orWhere('description', 'ilike', "%{$search}%");
        }

        if ($request->filled('sort') && !empty($request->input('sort'))) {
            $query->orderBy($request->input('sort'), $request->input('direction', 'asc'));
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $events = $query->paginate($request->input('per_page', 10))->appends($request->except('page'));

        return Inertia::render('Admin/Events/PrintCurrent', ['events' => $events->items()]);
    }

    public function printAll(Request $request)
    {
        $events = Event::orderBy('title')->get();

        $pdf = Pdf::loadView('pdf.events', ['events' => $events])->setPaper('a4', 'landscape');
        return $pdf->stream('events.pdf');
    }
}