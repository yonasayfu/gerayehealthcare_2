<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Http\Request;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\ExportConfig;

class EventService extends BaseService
{
    use ExportableTrait;

    public function __construct(Event $event)
    {
        parent::__construct($event);
    }

    protected function applySearch($query, $search)
    {
        $query->where('title', 'ilike', "%{$search}%")
              ->orWhere('description', 'ilike', "%{$search}%");
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, Event::class, ExportConfig::getEventConfig());
    }

    public function printSingle($id)
    {
        $event = $this->getById($id);
        $config = ExportConfig::getEventConfig()['single_record'];
        $config['title'] = 'Event Details - ' . $event->title;
        $config['document_title'] = 'Event Details';
        $config['filename'] = "event-{$event->id}.pdf";
        
        return $this->generateSingleRecordPdf($event, $config);
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, Event::class, ExportConfig::getEventConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, Event::class, ExportConfig::getEventConfig());
    }
}
