<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sender' => ['id' => $this->sender_id, 'name' => optional($this->sender)->name],
            'receiver' => ['id' => $this->receiver_id, 'name' => optional($this->receiver)->name],
            'message' => $this->message,
            'attachment_url' => $this->attachment_url,
            'attachment_filename' => $this->attachment_filename,
            'read_at' => optional($this->read_at)->toDateTimeString(),
            'created_at' => optional($this->created_at)->toDateTimeString(),
        ];
    }
}
