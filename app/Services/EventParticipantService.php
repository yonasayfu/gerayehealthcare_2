<?php

namespace App\Services;

use App\DTOs\CreateEventParticipantDTO;
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

    
}
