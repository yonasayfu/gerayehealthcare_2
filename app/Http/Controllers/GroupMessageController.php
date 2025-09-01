<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\GroupMessage;
use App\Models\Reaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupMessageController extends Controller
{
    protected function ensureMember(Group $group): void
    {
        $isMember = GroupMember::where('group_id', $group->id)
            ->where('user_id', Auth::id())
            ->exists();
        abort_unless($isMember, 403);
    }

    public function index(Request $request, Group $group)
    {
        $this->ensureMember($group);
        $messages = GroupMessage::with([
                'sender:id,name',
                'replyTo:id,message,sender_id,created_at',
                'reactions'
            ])
            ->where('group_id', $group->id)
            ->orderBy('created_at')
            ->get();
        return response()->json(['data' => $messages]);
    }

    public function store(Request $request, Group $group)
    {
        $this->ensureMember($group);
        $data = $request->validate([
            'message' => ['nullable', 'string', 'max:5000'],
            'reply_to_id' => ['nullable', 'integer', 'exists:group_messages,id'],
            'attachment' => ['nullable', 'file', 'max:20480'],
        ]);

        $path = null; $filename = null; $mime = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('group-messages/attachments', 'public');
            $filename = $file->getClientOriginalName();
            $mime = $file->getClientMimeType();
        }

        $msg = GroupMessage::create([
            'group_id' => $group->id,
            'sender_id' => Auth::id(),
            'message' => $data['message'] ?? null,
            'reply_to_id' => $data['reply_to_id'] ?? null,
            'attachment_path' => $path,
            'attachment_filename' => $filename,
            'attachment_mime_type' => $mime,
        ]);

        return response()->json(['data' => $msg->load('sender')], 201);
    }

    public function react(Request $request, Group $group, GroupMessage $message)
    {
        $this->ensureMember($group);
        abort_unless($message->group_id === $group->id, 404);
        $data = $request->validate(['emoji' => ['required', 'string', 'max:16']]);
        Reaction::firstOrCreate([
            'reactable_type' => GroupMessage::class,
            'reactable_id' => $message->id,
            'user_id' => Auth::id(),
            'emoji' => $data['emoji'],
        ]);
        return response()->noContent();
    }
}

