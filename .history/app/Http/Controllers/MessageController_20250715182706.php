<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Notifications\NewMessageReceived;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

        $conversationsQuery = User::where('id', '!=', $user->id);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $conversationsQuery->where('name', 'ilike', "%{$search}%")
                             ->orWhere('email', 'ilike', "%{$search}%");
        }

        $conversations = $conversationsQuery->orderBy('name', 'asc')->get();

        if ($recipient) {
            $selectedConversationUser = $recipient;
        } elseif ($conversations->isNotEmpty()) {
            $selectedConversationUser = $conversations->first();
        }

        if ($selectedConversationUser) {
            Message::where('sender_id', $selectedConversationUser->id)
                ->where('receiver_id', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

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
            'message' => 'nullable|string|max:1000',
            'attachment' => 'nullable|file|max:10240|mimes:jpeg,png,jpg,gif,pdf,doc,docx,xlsx,txt',
        ]);

        $messageContent = $validated['message'] ?? null;
        $attachmentPath = null;
        $attachmentFilename = null;
        $attachmentMimeType = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $originalFilename = $file->getClientOriginalName();
            $mimeType = $file->getClientMimeType();

            $filePath = $file->store('messages/attachments', 'public');

            $attachmentPath = $filePath;
            $attachmentFilename = $originalFilename;
            $attachmentMimeType = $mimeType;
        }

        if (empty($messageContent) && empty($attachmentPath)) {
            return response()->json(['error' => 'Message content or an attachment is required.'], 422);
        }

        Message::create([ // No need to assign to $message variable if not used after
            'sender_id' => Auth::id(),
            'receiver_id' => $validated['receiver_id'],
            'message' => $messageContent,
            'attachment_path' => $attachmentPath,
            'attachment_filename' => $attachmentFilename,
            'attachment_mime_type' => $attachmentMimeType,
        ]);

        // Dispatch the notification to the recipient (assuming NewMessageReceived notification is correctly set up)
        $recipient = User::find($validated['receiver_id']);
        if ($recipient) {
            // Note: If you need to pass the newly created message to the notification,
            // you'd retrieve it before this point. For now, assuming basic notification.
            // Example: $recipient->notify(new NewMessageReceived(Message::latest()->first())); // Not ideal, but illustrative
            // Best practice is to pass the $message object from the create() call if needed.
            // But since the current notification only uses properties from the message, and not the object itself, it's fine.
            $recipient->notify(new NewMessageReceived(new Message(['sender_id' => Auth::id(), 'receiver_id' => $validated['receiver_id'], 'message' => $messageContent])));
        }

        // IMPORTANT CHANGE: Return no content for Inertia AJAX requests
        return response()->noContent();
    }
}