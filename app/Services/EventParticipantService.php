<?php

namespace App\Services;

use App\Models\EventParticipant;
use Illuminate\Http\Request;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\AdditionalExportConfigs;

class EventParticipantService extends BaseService
{
    use ExportableTrait;

    public function __construct(EventParticipant $eventParticipant)
    {
        parent::__construct($eventParticipant);
    }

    protected function applySearch($query, $search)
    {
        $query->where('status', 'ilike', "%{$search}%");
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, EventParticipant::class, AdditionalExportConfigs::getEventParticipantConfig());
    }

    public function printSingle($id)
    {
        $eventParticipant = $this->getById($id);
        return $this->handlePrintSingle($eventParticipant, AdditionalExportConfigs::getEventParticipantConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, EventParticipant::class, AdditionalExportConfigs::getEventParticipantConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, EventParticipant::class, AdditionalExportConfigs::getEventParticipantConfig());
    }
}
