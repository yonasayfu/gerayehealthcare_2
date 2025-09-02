<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class NotificationController extends OptimizedBaseController
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
        $this->middleware('auth');
        $this->middleware('can:view-notifications');
    }

    /**
     * Display notifications interface
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get notifications with pagination
        $notifications = $this->notificationService->getUserNotifications(
            $user->id,
            $request->get('per_page', 20),
            $request->get('type'),
            $request->get('read_status')
        );

        $unreadCount = $this->notificationService->getUnreadCount($user->id);

        if ($request->wantsJson() || $request->ajax()) {
            return $this->success([
                'notifications' => $notifications->items(),
                'unread_count' => $unreadCount,
                'pagination' => [
                    'current_page' => $notifications->currentPage(),
                    'last_page' => $notifications->lastPage(),
                    'per_page' => $notifications->perPage(),
                    'total' => $notifications->total(),
                ],
            ], 'Notifications retrieved successfully');
        }

        return Inertia::render('Notifications/Index', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'filters' => $request->only(['type', 'read_status']),
        ]);
    }

    /**
     * Get unread notifications for header/sidebar
     */
    public function getUnread(Request $request)
    {
        $user = Auth::user();
        $limit = $request->get('limit', 10);

        $notifications = $this->notificationService->getUnreadNotifications($user->id, $limit);
        $unreadCount = $this->notificationService->getUnreadCount($user->id);

        return $this->success([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ], 'Unread notifications retrieved successfully');
    }

    /**
     * Mark a specific notification as read
     */
    public function markAsRead(Request $request, DatabaseNotification $notification)
    {
        try {
            // Authorization check
            if ($notification->notifiable_id !== Auth::id()) {
                return $this->error('Unauthorized action', 403);
            }

            $this->notificationService->markAsRead($notification);

            if ($request->wantsJson()) {
                return $this->success(null, 'Notification marked as read');
            }

            return redirect()->back()->with('success', 'Notification marked as read');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to mark notification as read');
        }
    }

    /**
     * Mark a specific notification as unread
     */
    public function markAsUnread(Request $request, DatabaseNotification $notification)
    {
        try {
            // Authorization check
            if ($notification->notifiable_id !== Auth::id()) {
                return $this->error('Unauthorized action', 403);
            }

            $this->notificationService->markAsUnread($notification);

            if ($request->wantsJson()) {
                return $this->success(null, 'Notification marked as unread');
            }

            return redirect()->back()->with('success', 'Notification marked as unread');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to mark notification as unread');
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        try {
            $user = Auth::user();
            $this->notificationService->markAllAsRead($user->id);

            if ($request->wantsJson()) {
                return $this->success(null, 'All notifications marked as read');
            }

            return redirect()->back()->with('success', 'All notifications marked as read');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to mark all notifications as read');
        }
    }

    /**
     * Delete a notification
     */
    public function destroy(Request $request, DatabaseNotification $notification)
    {
        try {
            // Authorization check
            if ($notification->notifiable_id !== Auth::id()) {
                return $this->error('Unauthorized action', 403);
            }

            $this->notificationService->delete($notification);

            if ($request->wantsJson()) {
                return $this->success(null, 'Notification deleted successfully');
            }

            return redirect()->back()->with('success', 'Notification deleted successfully');
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
            $deletedCount = $this->notificationService->deleteAllRead($user->id);

            if ($request->wantsJson()) {
                return $this->success(['deleted_count' => $deletedCount], 'Read notifications deleted successfully');
            }

            return redirect()->back()->with('success', "Deleted {$deletedCount} read notifications");
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to delete read notifications');
        }
    }

    /**
     * Get notification statistics
     */
    public function getStats(Request $request)
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
                'system_notifications',
            ]);

            $this->notificationService->updatePreferences($user->id, $preferences);

            if ($request->wantsJson()) {
                return $this->success($preferences, 'Notification preferences updated successfully');
            }

            return redirect()->back()->with('success', 'Notification preferences updated successfully');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to update notification preferences');
        }
    }
}
