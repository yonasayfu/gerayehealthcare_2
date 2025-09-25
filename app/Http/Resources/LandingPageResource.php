<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LandingPageResource extends JsonResource
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
            'page_code' => $this->page_code,
            'page_title' => $this->page_title,
            'page_url' => $this->page_url,
            'template_used' => $this->template_used,
            'language' => $this->language,
            'form_fields' => $this->form_fields,
            'conversion_goal' => $this->conversion_goal,
            'views' => $this->views,
            'submissions' => $this->submissions,
            'conversion_rate' => $this->conversion_rate,
            'is_active' => $this->is_active,
            'notes' => $this->notes,
            'campaign' => new MarketingCampaignResource($this->whenLoaded('campaign')),
        ];
    }
}
