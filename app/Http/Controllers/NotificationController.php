<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    /**
     * Fetch the authenticated user's unread notifications.
     */
    public function index()
    {
        $user = Auth::user();
        
        return response()->json([
            'unread_count' => $user->unreadNotifications->count(),
            'notifications' => $user->unreadNotifications->take(10), // Get up to 10 recent unread
        ]);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(DatabaseNotification $notification)
    {
        // Authorization: Ensure the notification belongs to the authenticated user
        if ($notification->notifiable_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $notification->markAsRead();

        return response()->noContent();
    }
}