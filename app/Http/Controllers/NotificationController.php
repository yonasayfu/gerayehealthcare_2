<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

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
        $user = Auth::user();
        Log::info('User roles:', ['roles' => $user->roles->pluck('name')]);

        if ($notification->notifiable_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $notification->markAsRead();

        return response()->noContent();
    }

    /**
     * Mark all notifications as read for the authenticated user.
     */
    public function markAllRead(Request $request)
    {
        $user = Auth::user();
        $user->unreadNotifications()->update(['read_at' => now()]);

        return response()->noContent();
    }
}
