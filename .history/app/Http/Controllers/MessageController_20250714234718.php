<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Notifications\NewMessageReceived;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia; // Keep this if you use Inertia::render directly in this controller, otherwise optional

class MessageController extends Controller
{
    /**
     * Fetches conversation list, selected conversation, and messages.
     * Handles initial selection if no recipient is specified.
     */
    public function getData(Request $request, User $recipient = null)
    {
        $user = Auth::user();
        $messages = [];
        $selectedConversationUser = null; // Initialize variable for the user representing the active conversation

        // --- 1. Fetch all possible conversations (users other than the current authenticated user) ---
        $conversationsQuery = User::where('id', '!=', $user->id);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $conversationsQuery->where('name', 'ilike', "%{$search}%")
                             ->orWhere('email', 'ilike', "%{$search}%"); // Added email search for conversations
        }

        $conversations = $conversationsQuery->orderBy('name', 'asc')->get();

        // --- 2. Determine the selected conversation ---
        if ($recipient) {
            // A specific recipient was passed via route parameter (e.g., /messages/data/123)
            $selectedConversationUser = $recipient;
        } elseif ($conversations->isNotEmpty()) {
            // No specific recipient, but there are conversations, so select the first one in the list
            $selectedConversationUser = $conversations->first();
        }

        // --- 3. Fetch messages for the selected conversation (if one exists) ---
        if ($selectedConversationUser) {
            // Mark messages from this sender as read by the current user
            Message::where('sender_id', $selectedConversationUser->id)
                ->where('receiver_id', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            // Fetch all messages between the authenticated user and the selected conversation user
            $messages = Message::where(function ($query) use ($user, $selectedConversationUser) {
                $query->where('sender_id', $user->id)
                      ->where('receiver_id', $selectedConversationUser->id);
            })->orWhere(function ($query) use ($user, $selectedConversationUser) {
                $query->where('sender_id', $selectedConversationUser->id)
                      ->where('receiver_id', $user->id);
            })->with(['sender', 'receiver'])->orderBy('created_at', 'asc')->get();
        }

        // --- 4. Return data as JSON response ---
        return response()->json([
            'conversations' => $conversations,
            // Load 'staff' relation for the selected user to display their position if available
            'selectedConversation' => $selectedConversationUser ? $selectedConversationUser->load('staff') : null,
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

        // Dispatch the notification to the recipient (assuming NewMessageReceived notification is correctly set up)
        $recipient = User::find($validated['receiver_id']);
        if ($recipient) {
            $recipient->notify(new NewMessageReceived($message));
        }

        // Return a JSON response for AJAX form submission
        return response()->json(['message' => 'Message sent successfully!', 'message_id' => $message->id]);
        // Removed: return redirect()->route('messages.index', ['recipient' => $request->receiver_id]);
    }
}