<?php

namespace App\Services\EventBroadcast;

use App\Http\Traits\ExportableTrait;
use App\Models\EventBroadcast;
use App\Notifications\EventBroadcastCreated;
use App\Notifications\EventBroadcastPublished;
use App\Models\EventStaffAssignment;
use App\Models\EventParticipant;
use App\Services\Base\BaseService;
use Illuminate\Support\Facades\Notification;

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

    public function create(array|object $data)
    {
        $created = parent::create($data);

        try {
            $created->load(['event']);
            // Notify users with permission to view broadcasts (admins/super admins)
            $recipients = \App\Models\User::permission('view event broadcasts')->get();
            if ($recipients->count() > 0) {
                Notification::send($recipients, new EventBroadcastCreated($created));
            }

            // Also notify assigned staff (their linked users) and event participants' users
            $staffUsers = EventStaffAssignment::with('staff.user')
                ->where('event_id', $created->event_id)
                ->get()
                ->pluck('staff.user')
                ->filter()
                ->unique('id')
                ->values();

            $participantUsers = EventParticipant::with('patient.user')
                ->where('event_id', $created->event_id)
                ->get()
                ->pluck('patient.user')
                ->filter()
                ->unique('id')
                ->values();

            $audience = collect()->merge($staffUsers)->merge($participantUsers)->unique('id')->values();
            if ($audience->count() > 0) {
                Notification::send($audience, new EventBroadcastPublished($created));
            }
        } catch (\Throwable $e) {
            \Log::warning('EventBroadcastCreated notification failed: '.$e->getMessage());
        }

        return $created;
    }
}
