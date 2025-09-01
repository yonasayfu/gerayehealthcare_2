<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketingCampaignResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'utm_source' => $this->utm_source,
            'utm_medium' => $this->utm_medium,
            'utm_campaign' => $this->utm_campaign,
            'utm_term' => $this->utm_term,
            'utm_content' => $this->utm_content,
            'budget' => $this->budget,
            'start_date' => $this->start_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'status' => $this->status,
            'target_audience' => $this->target_audience,
            'goals' => $this->goals,
            'leads_count' => $this->whenCounted('leads'),
            'conversions_count' => $this->when(isset($this->conversions_count), $this->conversions_count),
            'conversion_rate' => $this->when(isset($this->conversion_rate), $this->conversion_rate),
            'total_spent' => $this->when(isset($this->total_spent), $this->total_spent),
            'cost_per_lead' => $this->when(isset($this->cost_per_lead), $this->cost_per_lead),
            'roi' => $this->when(isset($this->roi), $this->roi),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            
            // Relationships
            'leads' => LeadResource::collection($this->whenLoaded('leads')),
        ];
    }
}
