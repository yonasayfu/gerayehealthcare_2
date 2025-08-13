<?php

namespace App\Services;

use App\DTOs\CreateEventBroadcastDTO;
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

    
}
