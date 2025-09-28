<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MarketingTaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'task_code' => $this->task_code,
            'task_type' => $this->task_type,
            'title' => $this->title,
            'description' => $this->description,
            'expected_results' => $this->expected_results,
            'scheduled_at' => $this->scheduled_at,
            'completed_at' => $this->completed_at,
            'status' => $this->status,
            'notes' => $this->notes,
            'campaign' => new MarketingCampaignResource($this->whenLoaded('campaign')),
            'assigned_to_staff' => new StaffResource($this->whenLoaded('assignedToStaff')),
            'related_content' => new CampaignContentResource($this->whenLoaded('relatedContent')),
            'doctor' => new StaffResource($this->whenLoaded('doctor')),
        ];
    }
}
