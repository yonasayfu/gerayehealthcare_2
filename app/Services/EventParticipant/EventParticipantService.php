<?php

namespace App\Services\EventParticipant;

use App\Http\Traits\ExportableTrait;
use App\Models\EventParticipant;
use App\Notifications\EventParticipantAssigned;
use App\Services\Base\BaseService;
use Illuminate\Http\Request;

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

    /**
     * Eager-load event and patient so UI can show names instead of raw IDs.
     */
    public function getAll(Request $request, array $with = [])
    {
        $with = ['event:id,title', 'patient:id,full_name'];

        return parent::getAll($request, $with);
    }

    public function getById(int $id, array $with = [])
    {
        $with = ['event:id,title', 'patient:id,full_name'];

        return parent::getById($id, $with);
    }

    public function create(array|object $data)
    {
        $participant = parent::create($data);

        // Notify the patient user if exists
        try {
            $participant->load(['event', 'patient.user']);
            $user = $participant->patient?->user;
            if ($user) {
                $user->notify(new EventParticipantAssigned($participant));
            }
        } catch (\Throwable $e) {
            // Swallow notification failures; do not block core flow
            \Log::warning('EventParticipant notification failed: '.$e->getMessage());
        }

        return $participant;
    }
}
