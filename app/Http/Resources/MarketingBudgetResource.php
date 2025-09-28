<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MarketingBudgetResource extends JsonResource
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
            'budget_name' => $this->budget_name,
            'description' => $this->description,
            'allocated_amount' => $this->allocated_amount,
            'spent_amount' => $this->spent_amount,
            'period_start' => $this->period_start,
            'period_end' => $this->period_end,
            'status' => $this->status,
            'campaign' => new MarketingCampaignResource($this->whenLoaded('campaign')),
            'platform' => new MarketingPlatformResource($this->whenLoaded('platform')),
        ];
    }
}
