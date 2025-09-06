<?php

namespace App\Services;

use App\Http\Config\ExportConfig;
use App\Http\Traits\ExportableTrait;
use App\Models\Event;
use App\Models\EventBroadcast;
use App\Models\EventParticipant;
use App\Models\EventStaffAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OptimizedEventService extends OptimizedBaseService
{
    use ExportableTrait;

    protected $cacheTtl = 1800; // 30 minutes for event data

    protected $shortCacheTtl = 300; // 5 minutes for real-time event updates

    public function __construct(Event $event)
    {
        parent::__construct($event);
    }

    public function getAll(Request $request, array $with = [])
    {
        $cacheKey = $this->generateCacheKey('all', $request->all(), $with);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($request, $with) {
            $query = $this->model->with(array_merge([
                'createdByStaff:id,first_name,last_name',
                'participants:id,event_id,patient_id,status',
                'staffAssignments:id,event_id,staff_id,role',
            ], $with));

            if ($request->has('search')) {
                $this->applySearch($query, $request->input('search'));
            }

            if ($request->has('region')) {
                $query->where('region', $request->input('region'));
            }

            if ($request->has('event_type')) {
                $query->where('is_free_service', $request->input('event_type') === 'free');
            }

            if ($request->has('status')) {
                $query->where('broadcast_status', $request->input('status'));
            }

            if ($request->has('date_from')) {
                $query->whereDate('event_date', '>=', $request->input('date_from'));
            }

            if ($request->has('date_to')) {
                $query->whereDate('event_date', '<=', $request->input('date_to'));
            }

            if ($request->has('sort')) {
                $direction = $request->input('direction', 'asc');
                $query->orderBy($request->input('sort'), $direction);
            } else {
                $query->orderBy('event_date', 'desc');
            }

            return $query->paginate($request->input('per_page', 10));
        });
    }

    public function getById(int $id, array $with = [])
    {
        $cacheKey = $this->generateCacheKey('single', ['id' => $id], $with);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($id, $with) {
            $with = array_unique(array_merge([
                'createdByStaff',
                'participants.patient:id,full_name,phone',
                'staffAssignments.staff:id,first_name,last_name,position',
                'broadcasts.sentByStaff:id,first_name,last_name',
                'recommendations.patient:id,full_name',
            ], $with));

            return $this->model->with($with)->findOrFail($id);
        });
    }

    public function create(array|object $data): Event
    {
        $data = is_object($data) ? (array) $data : $data;

        return DB::transaction(function () use ($data) {
            $event = parent::create($data);

            // Auto-assign staff if specified
            if (! empty($data['assigned_staff_ids'])) {
                $this->bulkAssignStaff($event->id, $data['assigned_staff_ids']);
            }

            // Auto-invite participants if specified
            if (! empty($data['participant_patient_ids'])) {
                $this->bulkInviteParticipants($event->id, $data['participant_patient_ids']);
            }

            // Clear related caches
            $this->clearEventCaches($event);

            return $event->load(['createdByStaff', 'participants', 'staffAssignments']);
        });
    }

    public function update(int $id, array|object $data): Event
    {
        $data = is_object($data) ? (array) $data : $data;

        return DB::transaction(function () use ($id, $data) {
            $event = parent::update($id, $data);

            // Update staff assignments if provided
            if (array_key_exists('assigned_staff_ids', $data)) {
                $this->updateStaffAssignments($event->id, $data['assigned_staff_ids']);
            }

            // Update participants if provided
            if (array_key_exists('participant_patient_ids', $data)) {
                $this->updateEventParticipants($event->id, $data['participant_patient_ids']);
            }

            // Clear related caches
            $this->clearEventCaches($event);

            return $event;
        });
    }

    /**
     * Bulk assign staff to an event
     */
    public function bulkAssignStaff(int $eventId, array $staffIds, string $role = 'Volunteer'): int
    {
        $assignments = collect($staffIds)->map(function ($staffId) use ($eventId, $role) {
            return [
                'event_id' => $eventId,
                'staff_id' => $staffId,
                'role' => $role,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        $count = EventStaffAssignment::insert($assignments);

        // Clear caches
        $this->clearCachePattern('event_*');

        return $count;
    }

    /**
     * Bulk invite participants to an event
     */
    public function bulkInviteParticipants(int $eventId, array $patientIds, string $status = 'Invited'): int
    {
        $participants = collect($patientIds)->map(function ($patientId) use ($eventId, $status) {
            return [
                'event_id' => $eventId,
                'patient_id' => $patientId,
                'status' => $status,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        $count = EventParticipant::insert($participants);

        // Clear caches
        $this->clearCachePattern('event_*');

        return $count;
    }

    /**
     * Update staff assignments for an event
     */
    protected function updateStaffAssignments(int $eventId, array $staffIds): void
    {
        // Remove existing assignments
        EventStaffAssignment::where('event_id', $eventId)->delete();

        // Add new assignments
        if (! empty($staffIds)) {
            $this->bulkAssignStaff($eventId, $staffIds);
        }
    }

    /**
     * Update event participants
     */
    protected function updateEventParticipants(int $eventId, array $patientIds): void
    {
        // Remove existing participants
        EventParticipant::where('event_id', $eventId)->delete();

        // Add new participants
        if (! empty($patientIds)) {
            $this->bulkInviteParticipants($eventId, $patientIds);
        }
    }

    /**
     * Get event statistics with caching
     */
    public function getEventStatistics(Request $request): array
    {
        $cacheKey = $this->generateCacheKey('statistics', $request->only(['region', 'date_from', 'date_to']));

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($request) {
            $query = $this->model->newQuery();

            // Apply filters
            if ($request->filled('region')) {
                $query->where('region', $request->input('region'));
            }

            if ($request->filled('date_from')) {
                $query->whereDate('event_date', '>=', $request->input('date_from'));
            }

            if ($request->filled('date_to')) {
                $query->whereDate('event_date', '<=', $request->input('date_to'));
            }

            return [
                'total_events' => $query->count(),
                'free_service_events' => (clone $query)->where('is_free_service', true)->count(),
                'paid_events' => (clone $query)->where('is_free_service', false)->count(),
                'upcoming_events' => (clone $query)->where('event_date', '>=', now())->count(),
                'past_events' => (clone $query)->where('event_date', '<', now())->count(),
                'events_by_status' => (clone $query)
                    ->selectRaw('broadcast_status, COUNT(*) as count')
                    ->groupBy('broadcast_status')
                    ->pluck('count', 'broadcast_status')
                    ->toArray(),
                'events_by_region' => (clone $query)
                    ->selectRaw('region, COUNT(*) as count')
                    ->groupBy('region')
                    ->orderByDesc('count')
                    ->pluck('count', 'region')
                    ->toArray(),
            ];
        });
    }

    /**
     * Get participation analytics
     */
    public function getParticipationAnalytics(int $eventId): array
    {
        $cacheKey = $this->generateCacheKey('participation_analytics', ['event_id' => $eventId]);

        return Cache::remember($cacheKey, $this->shortCacheTtl, function () use ($eventId) {
            $participantsQuery = EventParticipant::where('event_id', $eventId);

            return [
                'total_participants' => $participantsQuery->count(),
                'confirmed_participants' => (clone $participantsQuery)->where('status', 'Confirmed')->count(),
                'pending_participants' => (clone $participantsQuery)->where('status', 'Invited')->count(),
                'declined_participants' => (clone $participantsQuery)->where('status', 'Declined')->count(),
                'participation_by_status' => (clone $participantsQuery)
                    ->selectRaw('status, COUNT(*) as count')
                    ->groupBy('status')
                    ->pluck('count', 'status')
                    ->toArray(),
                'staff_assignments' => EventStaffAssignment::where('event_id', $eventId)
                    ->with('staff:id,first_name,last_name,position')
                    ->get()
                    ->toArray(),
            ];
        });
    }

    /**
     * Get upcoming events for dashboard
     */
    public function getUpcomingEvents(int $limit = 10): array
    {
        $cacheKey = $this->generateCacheKey('upcoming_events', ['limit' => $limit]);

        return Cache::remember($cacheKey, $this->shortCacheTtl, function () use ($limit) {
            return $this->model
                ->with(['createdByStaff:id,first_name,last_name'])
                ->where('event_date', '>=', now())
                ->orderBy('event_date')
                ->limit($limit)
                ->get()
                ->map(function ($event) {
                    return [
                        'id' => $event->id,
                        'title' => $event->title,
                        'event_date' => $event->event_date,
                        'location' => $event->location,
                        'region' => $event->region,
                        'is_free_service' => $event->is_free_service,
                        'created_by' => $event->createdByStaff ?
                            $event->createdByStaff->first_name.' '.$event->createdByStaff->last_name :
                            'Unknown',
                        'participant_count' => $event->participants()->count(),
                        'staff_count' => $event->staffAssignments()->count(),
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Send broadcast message to event participants
     */
    public function broadcastToParticipants(int $eventId, string $message, string $channel, int $sentByStaffId): array
    {
        return DB::transaction(function () use ($eventId, $message, $channel, $sentByStaffId) {
            // Get confirmed participants
            $participants = EventParticipant::where('event_id', $eventId)
                ->where('status', 'Confirmed')
                ->with('patient:id,phone,full_name')
                ->get();

            $broadcastData = [
                'event_id' => $eventId,
                'channel' => $channel,
                'message' => $message,
                'sent_by_staff_id' => $sentByStaffId,
                'recipient_count' => $participants->count(),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $broadcast = EventBroadcast::create($broadcastData);

            // Here you would integrate with SMS/Email service
            $sentCount = 0;
            foreach ($participants as $participant) {
                if ($participant->patient && $participant->patient->phone) {
                    // Simulate SMS sending (integrate with actual SMS service)
                    $sentCount++;
                }
            }

            // Clear related caches
            $this->clearCachePattern('event_*');

            return [
                'broadcast_id' => $broadcast->id,
                'recipients_count' => $participants->count(),
                'sent_count' => $sentCount,
                'failed_count' => $participants->count() - $sentCount,
            ];
        });
    }

    /**
     * Get events calendar data
     */
    public function getCalendarEvents(Request $request): array
    {
        $cacheKey = $this->generateCacheKey('calendar', $request->only(['start', 'end', 'region']));

        return Cache::remember($cacheKey, $this->shortCacheTtl, function () use ($request) {
            $query = $this->model->select([
                'id', 'title', 'event_date', 'location', 'region',
                'is_free_service', 'broadcast_status',
            ]);

            if ($request->filled('start')) {
                $query->whereDate('event_date', '>=', $request->input('start'));
            }

            if ($request->filled('end')) {
                $query->whereDate('event_date', '<=', $request->input('end'));
            }

            if ($request->filled('region')) {
                $query->where('region', $request->input('region'));
            }

            return $query->get()->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'start' => $event->event_date,
                    'location' => $event->location,
                    'region' => $event->region,
                    'backgroundColor' => $event->is_free_service ? '#28a745' : '#007bff',
                    'borderColor' => $event->is_free_service ? '#1e7e34' : '#0056b3',
                ];
            })->toArray();
        });
    }

    protected function applySearch($query, $search)
    {
        $query->where('title', 'ilike', "%{$search}%")
            ->orWhere('description', 'ilike', "%{$search}%")
            ->orWhere('location', 'ilike', "%{$search}%")
            ->orWhere('region', 'ilike', "%{$search}%");
    }

    /**
     * Clear event-related caches
     */
    protected function clearEventCaches($event): void
    {
        $patterns = [
            'event_*',
            'statistics_*',
            'participation_analytics_*',
            'upcoming_events_*',
            'calendar_*',
        ];

        foreach ($patterns as $pattern) {
            $this->clearCachePattern($pattern);
        }
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
