<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class NotificationService extends PerformanceOptimizedBaseService
{
    public function __construct()
    {
        // No specific model for this service
    }

    /**
     * Get user notifications with pagination and filtering
     */
    public function getUserNotifications(
        int $userId,
        int $perPage = 20,
        ?string $type = null,
        ?string $readStatus = null
    ): LengthAwarePaginator {
        $cacheKey = "user_notifications_{$userId}_{$perPage}_{$type}_{$readStatus}";

        return $this->getCachedData($cacheKey, function () use ($userId, $perPage, $type, $readStatus) {
            $query = DatabaseNotification::where('notifiable_id', $userId)
                ->where('notifiable_type', User::class)
                ->orderBy('created_at', 'desc');

            // Filter by type
            if ($type) {
                $query->whereJsonContains('data->type', $type);
            }

            // Filter by read status
            if ($readStatus === 'read') {
                $query->whereNotNull('read_at');
            } elseif ($readStatus === 'unread') {
                $query->whereNull('read_at');
            }

            return $query->paginate($perPage);
        }, 300); // Cache for 5 minutes
    }

    /**
     * Get unread notifications
     */
    public function getUnreadNotifications(int $userId, int $limit = 10): Collection
    {
        $cacheKey = "unread_notifications_{$userId}_{$limit}";

        return $this->getCachedData($cacheKey, function () use ($userId, $limit) {
            return DatabaseNotification::where('notifiable_id', $userId)
                ->where('notifiable_type', User::class)
                ->whereNull('read_at')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        }, 60); // Cache for 1 minute
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadCount(int $userId): int
    {
        $cacheKey = "unread_notifications_count_{$userId}";

        return $this->getCachedData($cacheKey, function () use ($userId) {
            return DatabaseNotification::where('notifiable_id', $userId)
                ->where('notifiable_type', User::class)
                ->whereNull('read_at')
                ->count();
        }, 60); // Cache for 1 minute
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(DatabaseNotification $notification): void
    {
        if (! $notification->read_at) {
            $notification->markAsRead();
            $this->clearUserNotificationCaches($notification->notifiable_id);
        }
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread(DatabaseNotification $notification): void
    {
        if ($notification->read_at) {
            $notification->update(['read_at' => null]);
            $this->clearUserNotificationCaches($notification->notifiable_id);
        }
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead(int $userId): int
    {
        $count = DatabaseNotification::where('notifiable_id', $userId)
            ->where('notifiable_type', User::class)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $this->clearUserNotificationCaches($userId);

        return $count;
    }

    /**
     * Delete notification
     */
    public function delete(DatabaseNotification $notification): void
    {
        $userId = $notification->notifiable_id;
        $notification->delete();
        $this->clearUserNotificationCaches($userId);
    }

    /**
     * Delete all read notifications for a user
     */
    public function deleteAllRead(int $userId): int
    {
        $count = DatabaseNotification::where('notifiable_id', $userId)
            ->where('notifiable_type', User::class)
            ->whereNotNull('read_at')
            ->delete();

        $this->clearUserNotificationCaches($userId);

        return $count;
    }

    /**
     * Get notification statistics for a user
     */
    public function getNotificationStats(int $userId): array
    {
        $cacheKey = "notification_stats_{$userId}";

        return $this->getCachedData($cacheKey, function () use ($userId) {
            $total = DatabaseNotification::where('notifiable_id', $userId)
                ->where('notifiable_type', User::class)
                ->count();

            $unread = DatabaseNotification::where('notifiable_id', $userId)
                ->where('notifiable_type', User::class)
                ->whereNull('read_at')
                ->count();

            $read = $total - $unread;

            // Get notifications by type
            $byType = DatabaseNotification::where('notifiable_id', $userId)
                ->where('notifiable_type', User::class)
                ->select(DB::raw("JSON_EXTRACT(data, '$.type') as type"), DB::raw('count(*) as count'))
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray();

            // Get recent activity (last 7 days)
            $recentActivity = DatabaseNotification::where('notifiable_id', $userId)
                ->where('notifiable_type', User::class)
                ->where('created_at', '>=', now()->subDays(7))
                ->selectRaw('DATE(created_at) as date, count(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('count', 'date')
                ->toArray();

            return [
                'total' => $total,
                'unread' => $unread,
                'read' => $read,
                'by_type' => $byType,
                'recent_activity' => $recentActivity,
            ];
        }, 600); // Cache for 10 minutes
    }

    /**
     * Update user notification preferences
     */
    public function updatePreferences(int $userId, array $preferences): void
    {
        $user = User::findOrFail($userId);

        // Update user preferences (assuming these fields exist on user model)
        $user->update($preferences);

        // Clear caches
        $this->clearUserNotificationCaches($userId);
    }

    /**
     * Send system notification to user
     */
    public function sendSystemNotification(int $userId, string $title, string $message, array $data = []): void
    {
        $user = User::findOrFail($userId);

        $notificationData = array_merge([
            'type' => 'system',
            'title' => $title,
            'message' => $message,
            'created_at' => now(),
        ], $data);

        $user->notify(new \App\Notifications\SystemNotification($notificationData));

        $this->clearUserNotificationCaches($userId);
    }

    /**
     * Send notification to multiple users
     */
    public function sendBulkNotification(array $userIds, string $title, string $message, array $data = []): void
    {
        $users = User::whereIn('id', $userIds)->get();

        $notificationData = array_merge([
            'type' => 'system',
            'title' => $title,
            'message' => $message,
            'created_at' => now(),
        ], $data);

        foreach ($users as $user) {
            $user->notify(new \App\Notifications\SystemNotification($notificationData));
            $this->clearUserNotificationCaches($user->id);
        }
    }

    /**
     * Clear user notification caches
     */
    private function clearUserNotificationCaches(int $userId): void
    {
        $patterns = [
            "user_notifications_{$userId}_*",
            "unread_notifications_{$userId}_*",
            "unread_notifications_count_{$userId}",
            "notification_stats_{$userId}",
        ];

        foreach ($patterns as $pattern) {
            $this->clearCachePattern($pattern);
        }
    }

    /**
     * Clear cache by pattern (if Redis is available)
     */
    private function clearCachePattern(string $pattern): void
    {
        try {
            if (config('cache.default') === 'redis') {
                $redis = app('redis');
                $keys = $redis->keys($pattern);
                if (! empty($keys)) {
                    $redis->del($keys);
                }
            }
        } catch (\Exception $e) {
            // Silently fail if Redis is not available
        }
    }
}
