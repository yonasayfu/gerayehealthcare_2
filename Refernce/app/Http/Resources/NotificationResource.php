<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = $this->data;

        return [
            'id' => $this->id,
            'type' => $data['type'] ?? 'system',
            'title' => $data['title'] ?? 'Notification',
            'message' => $data['message'] ?? '',
            'icon' => $data['icon'] ?? 'bell',
            'color' => $data['color'] ?? 'blue',
            'priority' => $data['priority'] ?? 'normal',
            'action_text' => $data['action_text'] ?? null,
            'action_url' => $data['action_url'] ?? null,
            'metadata' => $data['metadata'] ?? [],
            'is_read' => ! is_null($this->read_at),
            'read_at' => $this->read_at?->toISOString(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'formatted_date' => $this->created_at->diffForHumans(),

            // Additional fields for specific notification types
            'sender' => $this->when(isset($data['sender_name']), [
                'id' => $data['sender_id'] ?? null,
                'name' => $data['sender_name'] ?? null,
                'avatar' => $data['sender_avatar'] ?? null,
            ]),

            'message_preview' => $data['message_preview'] ?? null,
            'has_attachment' => $data['has_attachment'] ?? false,
        ];
    }
}
