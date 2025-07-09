<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MessageController extends Controller
{
    /**
     * Display the messages index page and a specific conversation.
     */
    public function index(User $recipient = null)
    {
        $user = Auth::user();
        $messages = [];

        // If a specific recipient is selected, fetch the conversation
        if ($recipient) {
            // Mark messages from the recipient as read
            Message::where('sender_id', $recipient->id)
                ->where('receiver_id', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            // Fetch the full conversation history
            $messages = Message::where(function ($query) use ($user, $recipient) {
                $query->where('sender_id', $user->id)
                      ->where('receiver_id', $recipient->id);
            })->orWhere(function ($query) use ($user, $recipient) {
                $query->where('sender_id', $recipient->id)
                      ->where('receiver_id', $user->id);
            })->with(['sender', 'receiver'])->orderBy('created_at', 'asc')->get();
        }

        // Get a list of all other users to display in the conversation list
        $conversations = User::where('id', '!=', $user->id)
            ->orderBy('name', 'asc')
            ->get();

        return Inertia::render('Messages/Index', [
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
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
        ]);

        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        return redirect()->route('messages.index', ['recipient' => $request->receiver_id]);
    }
}