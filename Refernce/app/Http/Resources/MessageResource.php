<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'message' => $this->message,
            'sender' => [
                'id' => $this->sender->id,
                'name' => $this->sender->name,
                'email' => $this->sender->email,
                'avatar' => $this->sender->profile_photo_path,
            ],
            'receiver' => [
                'id' => $this->receiver->id,
                'name' => $this->receiver->name,
                'email' => $this->receiver->email,
                'avatar' => $this->receiver->profile_photo_path,
            ],
            'attachment' => $this->when($this->hasAttachment(), [
                'path' => $this->attachment_path,
                'filename' => $this->attachment_filename,
                'mime_type' => $this->attachment_mime_type,
                'url' => $this->attachment_url,
                'size' => $this->formatted_file_size,
            ]),
            'priority' => $this->priority,
            'message_type' => $this->message_type,
            'is_read' => $this->isRead(),
            'read_at' => $this->read_at?->toISOString(),
            'parent_id' => $this->parent_id,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'formatted_date' => $this->created_at->diffForHumans(),
        ];
    }
}
