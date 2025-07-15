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
    public function index(Request $request)
    {
        $user = Auth::user();

        // If AJAX or expects JSON, return JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'unread_count' => $user->unreadNotifications->count(),
                'notifications' => $user->unreadNotifications->take(10),
            ]);
        }

        // Otherwise, return an Inertia page (if you have one)
        return Inertia::render('Notifications', [
            'unread_count' => $user->unreadNotifications->count(),
            'notifications' => $user->unreadNotifications->take(10),
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