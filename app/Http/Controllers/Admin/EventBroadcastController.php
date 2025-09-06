<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreateEventBroadcastDTO;
use App\Enums\RoleEnum;
use App\Http\Controllers\Base\BaseController;
use App\Http\Traits\ExportableTrait;
use App\Models\Event;
use App\Models\EventBroadcast;
use App\Models\Staff;
use App\Services\EventBroadcastService;
use App\Services\Validation\Rules\EventBroadcastRules;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EventBroadcastController extends BaseController
{
    use ExportableTrait;

    public function __construct(EventBroadcastService $eventBroadcastService)
    {
        parent::__construct(
            $eventBroadcastService,
            EventBroadcastRules::class,
            'Admin/EventBroadcasts',
            'broadcasts',
            EventBroadcast::class,
            CreateEventBroadcastDTO::class
        );
        $this->middleware('role:'.RoleEnum::SUPER_ADMIN->value.'|'.RoleEnum::ADMIN->value);
    }

    public function create()
    {
        $events = Event::select('id', 'title')->orderBy('title')->get();
        $staff = Staff::select('id', 'first_name', 'last_name')->orderBy('first_name')->get();

        return Inertia::render($this->viewName.'/Create', [
            'events' => $events,
            'staff' => $staff,
        ]);
    }

    public function edit($id)
    {
        $eventBroadcast = $this->service->getById($id);
        $events = Event::select('id', 'title')->orderBy('title')->get();
        $staff = Staff::select('id', 'first_name', 'last_name')->orderBy('first_name')->get();

        return Inertia::render($this->viewName.'/Edit', [
            lcfirst(class_basename($this->modelClass)) => $eventBroadcast,
            'events' => $events,
            'staff' => $staff,
        ]);
    }

    /**
     * Print current page/view (PDF with pagination and current filters).
     */
    public function printCurrent(Request $request)
    {
        $config = $this->buildExportConfig();

        return $this->handlePrintCurrent($request, EventBroadcast::class, $config);
    }

    /**
     * Minimal export/print config for Event Broadcasts compatible with ExportableTrait.
     */
    private function buildExportConfig(): array
    {
        $pdfColumns = [
            ['key' => 'id', 'label' => '#'],
            ['key' => 'event_id', 'label' => 'Event ID'],
            ['key' => 'channel', 'label' => 'Channel'],
            ['key' => 'message', 'label' => 'Message'],
            ['key' => 'sent_by_staff_id', 'label' => 'Sent By Staff ID'],
            ['key' => 'created_at', 'label' => 'Created At'],
        ];

        return [
            'pdf' => [
                'view' => 'pdf-layout',
                'document_title' => 'Event Broadcasts',
                'filename_prefix' => 'event_broadcasts',
                'orientation' => 'portrait',
                'columns' => $pdfColumns,
            ],
            'current_page' => [
                'view' => 'pdf-layout',
                'document_title' => 'Event Broadcasts (Current View)',
                'filename_prefix' => 'event_broadcasts_current',
                'orientation' => 'portrait',
                'columns' => $pdfColumns,
            ],
        ];
    }
}
