<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MarketingCampaignResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        $leadsCount = (int) ($this->leads_count ?? 0);
        $convertedCount = (int) ($this->converted_leads_count ?? 0);
        $conversionRate = $leadsCount > 0
            ? round(($convertedCount / $leadsCount) * 100, 2)
            : 0.0;

        return [
            'id' => $this->id,
            'campaign_code' => $this->campaign_code,
            'campaign_name' => $this->campaign_name,
            'campaign_type' => $this->campaign_type,
            'status' => $this->status,
            'urgency' => $this->urgency,
            'target_audience' => $this->target_audience,
            'budget_allocated' => $this->budget_allocated !== null ? (float) $this->budget_allocated : null,
            'budget_spent' => $this->budget_spent !== null ? (float) $this->budget_spent : null,
            'start_date' => $this->start_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'utm_campaign' => $this->utm_campaign,
            'utm_source' => $this->utm_source,
            'utm_medium' => $this->utm_medium,
            'assigned_staff_id' => $this->assigned_staff_id,
            'responsible_staff_id' => $this->responsible_staff_id,
            'created_by_staff_id' => $this->created_by_staff_id,
            'goals' => $this->goals,
            'leads_count' => $leadsCount,
            'converted_leads_count' => $convertedCount,
            'conversion_rate' => $conversionRate,
            'platform' => new MarketingPlatformResource($this->whenLoaded('platform')),
            'assigned_staff' => new StaffResource($this->whenLoaded('assignedStaff')),
            'responsible_staff' => new StaffResource($this->whenLoaded('responsibleStaff')),
            'created_by_staff' => new StaffResource($this->whenLoaded('createdByStaff')),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
