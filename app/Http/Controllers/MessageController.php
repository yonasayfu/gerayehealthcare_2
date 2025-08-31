<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Notifications\NewMessageReceived;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreMessageRequest;

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

        // Limit conversation list to authorized counterparts
        $conversationsQuery = User::where('id', '!=', $user->id)
            ->where(function ($q) use ($user) {
                // Staff can chat with other staff and assigned patients
                if ($user->staff) {
                    $q->whereHas('staff')
                      ->orWhereIn('id', function ($sub) use ($user) {
                          $sub->select('user_id')->from('patients')
                              ->whereIn('id', function ($sub2) use ($user) {
                                  $sub2->select('patient_id')->from('caregiver_assignments')
                                      ->where('staff_id', optional($user->staff)->id)
                                      ->where('status', 'Assigned');
                              });
                      });
                } else {
                    // Patient can chat only with assigned staff
                    $q->whereIn('id', function ($sub) use ($user) {
                        $sub->select('user_id')->from('staff')
                            ->whereIn('id', function ($sub2) use ($user) {
                                $sub2->select('staff_id')->from('caregiver_assignments')
                                    ->whereIn('patient_id', function ($sub3) use ($user) {
                                        $sub3->select('id')->from('patients')->where('user_id', $user->id);
                                    })
                                    ->where('status', 'Assigned');
                            });
                    });
                }
            });

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
    public function store(StoreMessageRequest $request)
    {
        $validated = $request->validated();

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

        // Authorization: ensure sender may message receiver
        $recipient = User::findOrFail($validated['receiver_id']);
        $this->authorize('communicate', $recipient);

        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $validated['receiver_id'],
            'message' => $messageContent,
            'attachment_path' => $attachmentPath,
            'attachment_filename' => $attachmentFilename,
            'attachment_mime_type' => $attachmentMimeType,
        ]);

        // Dispatch the notification to the recipient (assuming NewMessageReceived notification is correctly set up)
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

    /**
     * Delete a message.
     */
    public function destroy(Message $message)
    {
        // Ensure the authenticated user is either the sender or receiver of the message
        // Or, if you have an admin role, allow admins to delete any message
        if ($message->sender_id !== Auth::id() && $message->receiver_id !== Auth::id()) {
            // You might want to add role-based authorization here, e.g., if (Auth::user()->hasRole('admin'))
            return response()->json(['error' => 'Unauthorized to delete this message.'], 403);
        }

        // Optionally, delete the attachment file from storage
        if ($message->attachment_path) {
            Storage::disk('public')->delete($message->attachment_path);
        }

        $message->delete();

        return response()->noContent();
    }

    /**
     * Download an attachment.
     */
    public function downloadAttachment(Message $message)
    {
        // Ensure the authenticated user is part of the conversation
        if ($message->sender_id !== Auth::id() && $message->receiver_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized to download this attachment.'], 403);
        }

        if (!$message->attachment_path || !Storage::disk('public')->exists($message->attachment_path)) {
            return response()->json(['error' => 'Attachment not found.'], 404);
        }

        return Storage::download(storage_path('app/public/' . $message->attachment_path), $message->attachment_filename);
    }

    /**
     * Mark a message as read.
     */
    public function markAsRead(Message $message)
    {
        // Only the receiver can mark a message as read
        if ($message->receiver_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized to mark this message as read.'], 403);
        }

        $message->update(['read_at' => now()]);

        return response()->noContent();
    }

    /**
     * Mark a message as unread.
     */
    public function markAsUnread(Message $message)
    {
        // Only the receiver can mark a message as unread
        if ($message->receiver_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized to mark this message as unread.'], 403);
        }

        $message->update(['read_at' => null]);

        return response()->noContent();
    }
}
