<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Services\NotificationService;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends BaseApiController
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
        $this->middleware('auth:sanctum');
        $this->middleware('can:view-notifications');
    }

    /**
     * Get user notifications with pagination
     */
    public function index(Request $request)
    {
        try {
            $request->validate([
                'per_page' => 'integer|min:1|max:100',
                'type' => 'string',
                'read_status' => 'in:read,unread',
            ]);

            $user = Auth::user();
            $perPage = $request->get('per_page', 20);
            $type = $request->get('type');
            $readStatus = $request->get('read_status');

            $notifications = $this->notificationService->getUserNotifications(
                $user->id,
                $perPage,
                $type,
                $readStatus
            );

            $unreadCount = $this->notificationService->getUnreadCount($user->id);

            return $this->success([
                'notifications' => NotificationResource::collection($notifications->items()),
                'unread_count' => $unreadCount,
                'pagination' => [
                    'current_page' => $notifications->currentPage(),
                    'last_page' => $notifications->lastPage(),
                    'per_page' => $notifications->perPage(),
                    'total' => $notifications->total(),
                    'from' => $notifications->firstItem(),
                    'to' => $notifications->lastItem(),
                ],
            ], 'Notifications retrieved successfully');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to retrieve notifications');
        }
    }

    /**
     * Get unread notifications
     */
    public function unread(Request $request)
    {
        try {
            $request->validate([
                'limit' => 'integer|min:1|max:50',
            ]);

            $user = Auth::user();
            $limit = $request->get('limit', 10);

            $notifications = $this->notificationService->getUnreadNotifications($user->id, $limit);
            $unreadCount = $this->notificationService->getUnreadCount($user->id);

            return $this->success([
                'notifications' => NotificationResource::collection($notifications),
                'unread_count' => $unreadCount,
            ], 'Unread notifications retrieved successfully');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to retrieve unread notifications');
        }
    }

    /**
     * Mark notification as read
     */
    public function markRead(Request $request, DatabaseNotification $notification)
    {
        try {
            if ($notification->notifiable_id !== Auth::id()) {
                return $this->error('Unauthorized action', 403);
            }

            $this->notificationService->markAsRead($notification);

            return $this->success(null, 'Notification marked as read');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to mark notification as read');
        }
    }

    /**
     * Mark notification as unread
     */
    public function markUnread(Request $request, DatabaseNotification $notification)
    {
        try {
            if ($notification->notifiable_id !== Auth::id()) {
                return $this->error('Unauthorized action', 403);
            }

            $this->notificationService->markAsUnread($notification);

            return $this->success(null, 'Notification marked as unread');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to mark notification as unread');
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllRead(Request $request)
    {
        try {
            $user = Auth::user();
            $count = $this->notificationService->markAllAsRead($user->id);

            return $this->success([
                'marked_count' => $count,
            ], 'All notifications marked as read');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to mark all notifications as read');
        }
    }

    /**
     * Delete notification
     */
    public function destroy(Request $request, DatabaseNotification $notification)
    {
        try {
            if ($notification->notifiable_id !== Auth::id()) {
                return $this->error('Unauthorized action', 403);
            }

            $this->notificationService->delete($notification);

            return $this->success(null, 'Notification deleted successfully');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to delete notification');
        }
    }

    /**
     * Delete all read notifications
     */
    public function deleteAllRead(Request $request)
    {
        try {
            $user = Auth::user();
            $count = $this->notificationService->deleteAllRead($user->id);

            return $this->success([
                'deleted_count' => $count,
            ], 'Read notifications deleted successfully');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to delete read notifications');
        }
    }

    /**
     * Get notification statistics
     */
    public function stats(Request $request)
    {
        try {
            $user = Auth::user();
            $stats = $this->notificationService->getNotificationStats($user->id);

            return $this->success($stats, 'Notification statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to retrieve notification statistics');
        }
    }

    /**
     * Update notification preferences
     */
    public function updatePreferences(Request $request)
    {
        try {
            $request->validate([
                'email_notifications' => 'boolean',
                'push_notifications' => 'boolean',
                'message_notifications' => 'boolean',
                'system_notifications' => 'boolean',
            ]);

            $user = Auth::user();
            $preferences = $request->only([
                'email_notifications',
                'push_notifications',
                'message_notifications',
                'system_notifications'
            ]);

            $this->notificationService->updatePreferences($user->id, $preferences);

            return $this->success($preferences, 'Notification preferences updated successfully');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to update notification preferences');
        }
    }
}
