<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Notifications\NewMessageReceived;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia; // Keep if you use Inertia elsewhere in this controller

class MessageController extends Controller
{
    public function getData(Request $request, User $recipient = null)
    {
        $user = Auth::user();
        $messages = [];
        $selectedConversationUser = null; // Initialize a variable to hold the selected user

        // --- Fetch all possible conversations (users other than the current one) ---
        $conversationsQuery = User::where('id', '!=', $user->id);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $conversationsQuery->where('name', 'ilike', "%{$search}%")
                             ->orWhere('email', 'ilike', "%{$search}%"); // Added email search for conversations
        }

        $conversations = $conversationsQuery->orderBy('name', 'asc')->get();

        // --- Determine the selected conversation ---
        if ($recipient) {
            // A specific recipient was passed via route parameter
            $selectedConversationUser = $recipient;
        } elseif ($conversations->isNotEmpty()) {
            // No specific recipient, but there are conversations, so select the first one
            $selectedConversationUser = $conversations->first();
        }

        // --- Fetch messages for the selected conversation (if one exists) ---
        if ($selectedConversationUser) {
            // Mark messages from this sender as read
            Message::where('sender_id', $selectedConversationUser->id)
                ->where('receiver_id', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            // Fetch messages between the authenticated user and the selected conversation user
            $messages = Message::where(function ($query) use ($user, $selectedConversationUser) {
                $query->where('sender_id', $user->id)
                      ->where('receiver_id', $selectedConversationUser->id);
            })->orWhere(function ($query) use ($user, $selectedConversationUser) {
                $query->where('sender_id', $selectedConversationUser->id)
                      ->where('receiver_id', $user->id);
            })->with(['sender', 'receiver'])->orderBy('created_at', 'asc')->get();
        }

        return response()->json([
            'conversations' => $conversations,
            'selectedConversation' => $selectedConversationUser ? $selectedConversationUser->load('staff') : null, // Load staff for the selected user
            'messages' => $messages,
        ]);
    }

    // ... your store method remains unchanged ...
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

        $recipient = User::find($validated['receiver_id']);
        if ($recipient) {
            $recipient->notify(new NewMessageReceived($message));
        }

        // Return a JSON response for AJAX, not a redirect
        return response()->json(['message' => 'Message sent!', 'message_id' => $message->id]);
        // return redirect()->route('messages.index', ['recipient' => $request->receiver_id]); // This would cause a full page refresh
    }
}