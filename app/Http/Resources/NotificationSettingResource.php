<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationSettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'enable_push' => (bool) $this->enable_push,
            'enable_email' => (bool) $this->enable_email,
            'enable_sms' => (bool) $this->enable_sms,
            'channels' => $this->channels ?? [],
            'enable_notifications' => (bool) ($this->enable_push || $this->enable_email),
        ];
    }
}
