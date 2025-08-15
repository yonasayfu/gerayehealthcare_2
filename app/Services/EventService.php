<?php

namespace App\Services;

use App\DTOs\CreateEventDTO;
use App\Models\Event;
use Illuminate\Http\Request;

class EventService extends BaseService
{
    public function __construct(Event $event)
    {
        parent::__construct($event);
    }

    protected function applySearch($query, $search)
    {
        $query->where('title', 'ilike', "%{$search}%")
              ->orWhere('description', 'ilike', "%{$search}%");
    }
}
