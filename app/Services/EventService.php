<?php

namespace App\Services;

use App\Http\Config\ExportConfig;
use App\Http\Traits\ExportableTrait;
use App\Models\Event;
use Illuminate\Http\Request;

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
        // Force CSV-only export for Events
        $request->merge(['format' => 'csv']);

        return $this->handleExport($request, Event::class, ExportConfig::getEventConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, Event::class, ExportConfig::getEventConfig());
    }

    public function printSingle(Request $request, Event $event)
    {
        return $this->handlePrintSingle($request, $event, ExportConfig::getEventConfig());
    }
}
