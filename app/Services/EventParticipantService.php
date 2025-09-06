<?php

namespace App\Services;

use App\Http\Traits\ExportableTrait;
use App\Models\EventParticipant;

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
