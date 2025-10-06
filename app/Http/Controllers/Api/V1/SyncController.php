<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\PatientResource;
use App\Http\Resources\StaffResource;
use App\Http\Resources\VisitServiceResource;
use App\Models\Patient;
use App\Models\Staff;
use App\Models\VisitService;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SyncController extends BaseApiController
{
    public function changes(Request $request)
    {
        $sinceParam = $request->query('since');
        $limit = (int) $request->query('limit', 500);
        $limit = $limit > 0 ? min($limit, 2000) : 500;

        try {
            $since = $sinceParam ? Carbon::parse($sinceParam) : null;
        } catch (\Throwable $e) {
            $since = null;
        }

        $since ??= now()->subDays(30);
        $generatedAt = now();

        $patients = Patient::query()
            ->where('updated_at', '>', $since)
            ->orderBy('updated_at')
            ->limit($limit)
            ->get();

        $staff = Staff::query()
            ->where('updated_at', '>', $since)
            ->orderBy('updated_at')
            ->limit($limit)
            ->get();

        $visitServices = VisitService::query()
            ->with(['patient', 'staff'])
            ->where('updated_at', '>', $since)
            ->orderBy('updated_at')
            ->limit($limit)
            ->get();

        $notifications = DatabaseNotification::query()
            ->where('notifiable_id', $request->user()?->id)
            ->where('updated_at', '>', $since)
            ->orderBy('updated_at')
            ->limit($limit)
            ->get()
            ->map(static fn(DatabaseNotification $notification) => [
                  'id' => $notification->id,
                  'type' => $notification->type,
                  'data' => $notification->data,
                  'read_at' => $notification->read_at?->toDateTimeString(),
                  'created_at' => $notification->created_at?->toDateTimeString(),
                  'updated_at' => $notification->updated_at?->toDateTimeString(),
                ])
            ->values()
            ->all();

        return $this->successResponse([
            'patients' => PatientResource::collection($patients),
            'staff' => StaffResource::collection($staff),
            'visit_services' => VisitServiceResource::collection($visitServices),
            'notifications' => $notifications,
            'meta' => [
                'since' => $since->toIso8601String(),
                'generated_at' => $generatedAt->toIso8601String(),
                'limit' => $limit,
            ],
        ]);
    }
}
