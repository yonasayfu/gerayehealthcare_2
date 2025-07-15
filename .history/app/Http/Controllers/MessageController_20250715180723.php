<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Notifications\NewMessageReceived;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // Import Storage facade

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
        $selectedConversationUser = null;

        // --- 1. Fetch all possible conversations (users other than the current authenticated user) ---
        $conversationsQuery = User::where('id', '!=', $user->id);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $conversationsQuery->where('name', 'ilike', "%{$search}%")
                             ->orWhere('email', 'ilike', "%{$search}%");
        }

        $conversations = $conversationsQuery->orderBy('name', 'asc')->get();

        // --- 2. Determine the selected conversation ---
        if ($recipient) {
            $selectedConversationUser = $recipient;
        } elseif ($conversations->isNotEmpty()) {
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
            'message' => 'nullable|string|max:1000', // Message can be nullable if only attachment
            'attachment' => 'nullable|file|max:10240|mimes:jpeg,png,jpg,gif,pdf,doc,docx,xlsx,txt', // Max 10MB, common types
        ]);

        $messageContent = $validated['message'] ?? null;
        $attachmentPath = null;
        $attachmentFilename = null;
        $attachmentMimeType = null;

        // Handle attachment upload
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $originalFilename = $file->getClientOriginalName();
            $mimeType = $file->getClientMimeType();

            // Store the file in the 'messages/attachments' directory within 'public' disk
            $filePath = $file->store('messages/attachments', 'public');

            $attachmentPath = $filePath;
            $attachmentFilename = $originalFilename;
            $attachmentMimeType = $mimeType;
        }

        // Ensure either message content or an attachment is present
        if (empty($messageContent) && empty($attachmentPath)) {
            return response()->json(['error' => 'Message content or an attachment is required.'], 422);
        }

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $validated['receiver_id'],
            'message' => $messageContent,
            'attachment_path' => $attachmentPath,
            'attachment_filename' => $attachmentFilename,
            'attachment_mime_type' => $attachmentMimeType,
        ]);

        // Dispatch the notification to the recipient
        $recipient = User::find($validated['receiver_id']);
        if ($recipient) {
            $recipient->notify(new NewMessageReceived($message));
        }

        return response()->json(['message' => 'Message sent successfully!', 'message_id' => $message->id]);
    }
}