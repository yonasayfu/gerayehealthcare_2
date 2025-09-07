<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
{
    /** @param Request $request */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'created_by' => $this->created_by,
            'members_count' => $this->when(isset($this->members_count), $this->members_count),
            'latestMessage' => $this->whenLoaded('latestMessage', function () {
                return [
                    'id' => $this->latestMessage->id,
                    'message' => $this->latestMessage->message,
                    'sender' => [
                        'id' => $this->latestMessage->sender->id ?? null,
                        'name' => $this->latestMessage->sender->name ?? null,
                    ],
                    'created_at' => $this->latestMessage->created_at,
                ];
            }),
            'users' => $this->whenLoaded('users', function () {
                return $this->users->map(function ($u) {
                    return [
                        'id' => $u->id,
                        'name' => $u->name,
                        'email' => $u->email,
                    ];
                });
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
