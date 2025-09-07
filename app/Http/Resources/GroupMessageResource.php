<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupMessageResource extends JsonResource
{
    /** @param Request $request */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'group_id' => $this->group_id,
            'sender_id' => $this->sender_id,
            'sender' => $this->whenLoaded('sender', function () {
                return [
                    'id' => $this->sender->id,
                    'name' => $this->sender->name,
                ];
            }),
            'message' => $this->message,
            'reply_to_id' => $this->reply_to_id,
            'attachment_path' => $this->attachment_path,
            'attachment_filename' => $this->attachment_filename,
            'attachment_mime_type' => $this->attachment_mime_type,
            'priority' => $this->priority,
            'message_type' => $this->message_type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
