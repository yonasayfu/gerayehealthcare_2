<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CampaignContentResource extends JsonResource
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
            'content_type' => $this->content_type,
            'title' => $this->title,
            'description' => $this->description,
            'media_url' => $this->media_url,
            'scheduled_post_date' => $this->scheduled_post_date,
            'actual_post_date' => $this->actual_post_date,
            'status' => $this->status,
            'engagement_metrics' => $this->engagement_metrics,
            'campaign' => new MarketingCampaignResource($this->whenLoaded('campaign')),
            'platform' => new MarketingPlatformResource($this->whenLoaded('platform')),
        ];
    }
}
