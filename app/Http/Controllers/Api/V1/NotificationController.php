<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $notifications = $user->notifications()->latest()->paginate($request->integer('per_page', 20));
        $unread = $user->unreadNotifications()->count();
        return response()->json(['data' => $notifications->items(), 'meta' => ['unread_count' => $unread, 'current_page' => $notifications->currentPage()]]);
    }

    public function markRead(Request $request, DatabaseNotification $notification)
    {
        if ($notification->notifiable_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $notification->markAsRead();
        return response()->noContent();
    }
}

