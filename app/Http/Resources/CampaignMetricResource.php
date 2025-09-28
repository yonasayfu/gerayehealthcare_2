<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CampaignMetricResource extends JsonResource
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
            'date' => $this->date,
            'impressions' => $this->impressions,
            'clicks' => $this->clicks,
            'conversions' => $this->conversions,
            'cost_per_click' => $this->cost_per_click,
            'cost_per_conversion' => $this->cost_per_conversion,
            'roi_percentage' => $this->roi_percentage,
            'leads_generated' => $this->leads_generated,
            'patients_acquired' => $this->patients_acquired,
            'revenue_generated' => $this->revenue_generated,
            'engagement_rate' => $this->engagement_rate,
            'reach' => $this->reach,
            'campaign' => new MarketingCampaignResource($this->whenLoaded('campaign')),
        ];
    }
}
