<?php

namespace App\Services;

use App\Models\EventBroadcast;
use Illuminate\Http\Request;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\AdditionalExportConfigs;

class EventBroadcastService extends BaseService
{
    use ExportableTrait;

    public function __construct(EventBroadcast $eventBroadcast)
    {
        parent::__construct($eventBroadcast);
    }

    protected function applySearch($query, $search)
    {
        $query->where('message', 'ilike', "%{$search}%")
              ->orWhere('channel', 'ilike', "%{$search}%");
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, EventBroadcast::class, AdditionalExportConfigs::getEventBroadcastConfig());
    }

    public function printSingle($id)
    {
        $eventBroadcast = $this->getById($id);
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
