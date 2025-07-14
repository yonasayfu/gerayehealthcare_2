<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Notifications\NewMessageReceived; // 1. Import the new Notification class
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MessageController extends Controller
{
    // The index() method remains unchanged...
    public function getData(Request $request, User $recipient = null)
    {
        $user = Auth::user();
        $messages = [];

        if ($recipient) {
            Message::where('sender_id', $recipient->id)
                ->where('receiver_id', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            $messages = Message::where(function ($query) use ($user, $recipient) {
                $query->where('sender_id', $user->id)
                      ->where('receiver_id', $recipient->id);
            })->orWhere(function ($query) use ($user, $recipient) {
                $query->where('sender_id', $recipient->id)
                      ->where('receiver_id', $user->id);
            })->with(['sender', 'receiver'])->orderBy('created_at', 'asc')->get();
        }

        $conversations = User::where('id', '!=', $user->id);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $conversations->where('name', 'ilike', "%{$search}%");
        }

        $conversations = $conversations->orderBy('name', 'asc')->get();

        return response()->json([
            'conversations' => $conversations,
            'selectedConversation' => $recipient ? $recipient->load('staff') : null,
            'messages' => $messages,
        ]);
    }

    /**
     * Store a new message in the database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $validated['receiver_id'],
            'message' => $validated['message'],
        ]);

        // 2. Dispatch the notification to the recipient
        $recipient = User::find($validated['receiver_id']);
        if ($recipient) {
            $recipient->notify(new NewMessageReceived($message));
        }

        return redirect()->route('messages.index', ['recipient' => $request->receiver_id]);
    }
}