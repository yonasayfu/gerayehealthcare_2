<?php

namespace App\Services;

use App\Http\Traits\ExportableTrait;
use App\Models\EventBroadcast;

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

    /**
     * Eager-load relations so UI can show names instead of IDs.
     */
    public function getAll(\Illuminate\Http\Request $request, array $with = [])
    {
        $with = ['event:id,title', 'sentByStaff:id,first_name,last_name'];

        return parent::getAll($request, $with);
    }

    public function getById(int $id, array $with = [])
    {
        $with = ['event:id,title', 'sentByStaff:id,first_name,last_name'];

        return parent::getById($id, $with);
    }
}
